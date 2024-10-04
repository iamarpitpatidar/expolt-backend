<?php

namespace App\Jobs;

use App\Models\Settings;
use App\Models\VirtualMachine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DestroyIdleMachines implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {}

    /**
     * Execute the job.
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $idleTimeout = Settings::query()->where('key', 'idle_timeout')->first()->value('value');
        $idleTimeoutDuration = Carbon::now()->subMinutes($idleTimeout);

        $vms = VirtualMachine::query()
            ->where('last_active', '<=', $idleTimeoutDuration)
            ->get();

        foreach ($vms as $vm) {
            // Destroy the virtual machine on DigitalOcean
            $response = Http::withHeader('Authorization', 'Bearer ' . config('vm.api_key'))
                ->delete('https://api.digitalocean.com/v2/droplets/' . $vm->meta['id']);

            if ($response->status() === 204) {
                $vm->delete();
            } else {
                // failed
                /** @var array{message:string} $json */
                $json = $response->json();
                Log::error($json['message']);
            }
        }
    }
}
