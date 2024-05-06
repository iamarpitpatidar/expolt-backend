<?php

namespace App\Jobs;

use App\Models\VirtualMachine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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
        $machine = VirtualMachine::query()->where('uuid', $this->uuid)->first();
        if (!$machine) return;

        $machine->current_state = 'provisioning';
        $response = Http::withHeader('Authorization', 'Bearer '.config('vm.api_key'))
            ->post('https://api.vultr.com/v2/instances', [
                'region' => config('vm.region'),
                'plan' => config('vm.plan'),
                'os_id' => config('vm.os_id'),
                'label' => config('vm.label'),
                'tag' => config('vm.tag'),
            ])->json();

        if (isset($response['error'])) {
            $machine->current_state = 'failed';
        } else {
            $machine->current_state = 'running';
            $machine->meta = $response['instance'];
        }
        $machine->save();
    }
}
