<?php

namespace App\Http\Controllers;

use App\Jobs\CreateVMJob;
use App\Models\App;
use App\Models\VirtualMachine;
use App\Transformers\VirtualMachineTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VirtualMachineController extends Controller
{
    public function show(string $appUUid): JsonResponse
    {
        $app = App::active()->findByUUID($appUUid)->first();
        if (!$app) {
            return $this->sendErrorResponse('App not found', 400);
        }

        if (
            !VirtualMachine::query()
                ->where('user_id', auth()->user()?->id)
                ->whereIn('current_state', ['running', 'provisioning'])
                ->exists()
        ) {
            $uuid = Str::uuid();

            $machine = VirtualMachine::query()->create([
                'uuid' => $uuid,
                'user_id' => auth()->user()?->id,
                'current_state' => 'pending',
                'meta' => []
            ]);
            dispatch(new CreateVMJob($uuid));
        } else {
            $machine = VirtualMachine::query()
                ->where('user_id', auth()->user()?->id)
                ->first();
        }

        $vm = fractal($machine, new VirtualMachineTransformer())->toArray();

        return $this->sendResponse(['machine' => $vm]);
    }

    public function whoAmI(Request $request): JsonResponse
    {
        $client_ip = $request->ip();
        $virtualMachines = VirtualMachine::query()
            ->where('meta', 'like', '' . $client_ip . '%')
            ->get();

        $virtualMachine = $virtualMachines->first(function ($vm) use ($client_ip) {
            foreach ($vm->meta['networks'] as $network) {
                if ($network['ip_address'] === $client_ip) {
                    return true;
                }
            }
            return false;
        });
        if (!$virtualMachine) {
            return $this->forbiddenError();
        }

        return $this->sendResponse(['machine' => $virtualMachine->uuid]);
    }
}
