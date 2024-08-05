<?php

namespace App\Jobs;

use App\Models\VirtualMachine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class VMHealthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $machineUUID;
    protected string $statusURL;
    protected int $attempt;

    /**
     * Create a new job instance.
     */
    public function __construct(string $machineUUID, string $statusURL, int $attempt = 1)
    {
        $this->machineUUID = $machineUUID;
        $this->statusURL = $statusURL;
        $this->attempt = $attempt;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $machine = VirtualMachine::query()->where('uuid', $this->machineUUID)->first();
        if (!$machine) return;

        $response = Http::withHeader('Authorization', 'Bearer '.config('vm.api_key'))
            ->get($this->statusURL);
        $response = $response->json();

        if (isset($response['action'])) {
            $action = $response['action'];
            $status = ['in-progress' => 'provisioning', 'completed' => 'running'];
            if ($action['status'] === 'completed') {
                $machine->current_state = $status[$action['status']];
                $machine->save();
                $this->updateDroplet($action['resource_id']);
                return;
            } elseif ($action['status'] === 'in-progress') {
                dispatch(new VMHealthJob($this->machineUUID, $this->statusURL, $this->attempt + 1))
                    ->delay(now()->addSeconds($this->attempt >= 4 ? 10 : 30));
            } else {
                $machine->current_state = 'failed';
            }
        } else {
            $machine->current_state = 'failed';
        }

        $machine->save();
    }

    private function updateDroplet(string $dropletId): void
    {
        $response = Http::withHeader('Authorization', 'Bearer '.config('vm.api_key'))
            ->get('https://api.digitalocean.com/v2/droplets/'.$dropletId);
        $response = $response->json();

        if (isset($response['droplet'])) {
            $droplet = $response['droplet'];
            $machine = VirtualMachine::query()->where('uuid', $this->machineUUID)->first();
            if (!$machine) return;

            $machine->meta = [
                'id' => $droplet['id'],
                'name' => $droplet['name'],
                'networks' => $droplet['networks']['v4']
            ];
            $machine->save();
        }
    }
}
