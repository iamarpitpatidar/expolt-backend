<?php

namespace App\Jobs;

use App\Models\VirtualMachine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CreateVMJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $uuid;

    /**
     * Create a new job instance.
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var VirtualMachine|null $machine */
        $machine = VirtualMachine::query()->where('uuid', $this->uuid)->first();
        if (!$machine) return;

        $similarMachine = VirtualMachine::query()->where('user_id', auth()->id())
            ->where('current_state', 'running')
            ->first();
        if ($similarMachine) {
            $machine->current_state = 'running';
            $machine->meta = $similarMachine->meta;
            $machine->save();
        } else {
            $machine->current_state = 'provisioning';
            $machine->save();
            $this->provisionVM($machine);
        }
    }

    /**
     * @throws ConnectionException
     */
    private function provisionVM(VirtualMachine $machine): void
    {
        $response = Http::withHeader('Authorization', 'Bearer '.config('vm.api_key'))
            ->post('https://api.digitalocean.com/v2/droplets', [
                'name' => 'expolt-vm-'.$machine->id,
                'region' => config('vm.region'),
                'size' => config('vm.size'),
                'image' => config('vm.snapshot'),
                'ssh_keys' => config('vm.ssh_keys'),
                'monitoring' => true,
                'tags' => array_merge(config('vm.tags'), ['expolt_user_'.$machine->user_id]), // @phpstan-ignore-line
                'with_droplet_agent' => false
            ]);

        $status = $response->status();
        if ($status !== 202) {
            $machine->current_state = 'failed';
            $machine->save();
            return;
        }

        $response = $response->json();
        if (isset($response['links']['actions']) && count($response['links']['actions'])) { // @phpstan-ignore-line
            /** @var array{href:string, rel:string} $action */
            $action = $response['links']['actions'][0];
            if ($action['rel'] === 'create') {
                dispatch(new VMHealthJob($machine->uuid, $action['href']));
            }
        } else {
            $machine->current_state = 'failed';
            $machine->save();
        }
    }
}
