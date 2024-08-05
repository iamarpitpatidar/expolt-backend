<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DestroyVirtualMachinesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     * @throws ConnectionException
     */
    public function handle(): void
    {
        if (!$this->userId) {
            return;
        }

        // Destroy virtual machines
        $response = Http::withHeader('Authorization', 'Bearer '.config('vm.api_key'))
            ->delete('https://api.digitalocean.com/v2/droplets?tag_name=expolt_user:'.$this->userId);

        if ($response->status() !== 204) {
            // failed
            $response = $response->json();
            Log::error($response['message']);

            $this->fail($response['message']);
        }
    }
}
