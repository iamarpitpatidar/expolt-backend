<?php

namespace App\Http\Controllers;

use App\Jobs\CreateVMJob;
use App\Models\App;
use App\Models\VirtualMachine;
use App\Transformers\VirtualMachineTransformer;
use Illuminate\Http\JsonResponse;
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
}
