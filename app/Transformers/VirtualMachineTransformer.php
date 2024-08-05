<?php

namespace App\Transformers;

use App\Models\VirtualMachine;
use League\Fractal\TransformerAbstract;

class VirtualMachineTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param VirtualMachine $machine
     * @return array<string, string>
     */
    public function transform(VirtualMachine $machine): array
    {
        $meta = $machine->meta;
        $networks = $meta['networks'] ?? [];
        return [
            'id' => $machine->uuid,
            'state' => $machine->current_state,
            'redirectTo' => $this->getRedirectURL($networks),
        ];
    }

    private function getRedirectURL(array $networks): string
    {
        $port = 8006;
        foreach ($networks as $network) {
            if ($network['type'] === 'public') {
                return 'http://'.$network['ip_address'].':'.$port;
            }
        }

        return '';
    }
}
