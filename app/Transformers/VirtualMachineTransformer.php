<?php

namespace App\Transformers;

use App\Models\VirtualMachine;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

class VirtualMachineTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = ['meta'];

    /**
     * A Fractal transformer.
     *
     * @param VirtualMachine $machine
     * @return array<string, string>
     */
    public function transform(VirtualMachine $machine): array
    {
        return [
            'id' => $machine->uuid,
            'state' => $machine->current_state,
        ];
    }

    public function includeMeta(VirtualMachine $machine): Primitive
    {
        $meta = $machine->meta;
        $ip = $meta['ip'] ?? '';
        $port = $meta['port'] ?? '';

        return $this->primitive($meta);
        return $this->primitive([
            'redirectURL' => $ip && $port ? "http://$ip:$port" : '',
        ]);
    }
}
