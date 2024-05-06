<?php

namespace App\Http\Controllers;

use App\Jobs\CreateVMJob;
use App\Models\App;
use App\Models\VirtualMachine;
use App\Transformers\VirtualMachineTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VirtualMachineController extends Controller
{
    public function show( App $app): JsonResponse
    {
        if (
            !VirtualMachine::query()
                ->where('app_id', $app->id)
                ->where('user_id', auth()->user()?->id)
                ->exists()
        ) {
            $uuid = Str::uuid();

            $machine = VirtualMachine::query()->create([
                'uuid' => $uuid,
                'app_id' => $app->id,
                'user_id' => auth()->user()?->id,
                'current_state' => 'pending',
                'meta' => []
            ]);
            dispatch(new CreateVMJob($uuid));
        } else {
            $machine = VirtualMachine::query()
                ->where('user_id', auth()->user()?->id)
                ->where('app_id', $app->id)->first();
        }

        $vm = fractal($machine, new VirtualMachineTransformer())->toArray();

        return $this->sendResponse(['machine' => $vm]);
    }

    private function createInstance()
    {
        $response = Http::withHeader('Authorization', 'Bearer '.config('vm.api_key'))
            ->post('https://api.vultr.com/v2/instances', [
                'region' => config('vm.region'),
//                'plan' => config('vm.plan'),
                'os_id' => config('vm.os_id'),
                'label' => config('vm.label'),
                'tag' => config('vm.tag'),
            ])->json();

        if (isset($response['error'])) {
            // update machine failed
        } else {
            // update machine success
        }

        return $this->sendResponse(['machine' => $response]);
    }
}
