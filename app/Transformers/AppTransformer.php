<?php

namespace App\Transformers;

use App\Models\App;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

class AppTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array<string>
     */
    protected array $availableIncludes = ['uuid', 'status'];

    /**
     * A Fractal transformer.
     *
     * @param App $app
     * @return array<string, string|int>
     */
    public function transform(App $app): array
    {
        return [
            'id' => $app->id,
            'name' => $app->name,
            'description' => $app->description,
            'type' => $app->type,
            'uuid' => $app->uuid,
            'meta' => $app->meta
        ];
    }

    public function includeUuid(App $app): Primitive
    {
        return $this->primitive($app->uuid);
    }

    public function includeStatus(App $app): Primitive
    {
        return $this->primitive($app->status);
    }
}
