<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array<string>
     */
    protected array $availableIncludes = ['status'];

    /**
     * A Fractal transformer.
     *
     * @param User $user
     * @return array<string, string>
     */
    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles()->first()?->name,
        ];
    }

    /**
     * Include Status
     *
     * @param User $user
     * @return Primitive
     */
    public function includeStatus(User $user): Primitive
    {
        return $this->primitive($user->status);
    }
}
