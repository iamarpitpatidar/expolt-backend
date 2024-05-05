<?php

namespace App\Transformers;

use App\Models\App;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

class AppTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = ['status'];
    /**
     * List of resources possible to include
     *
     * @var array<string>
     */
    protected array $availableIncludes = ['status'];

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
            'meta' => $app->meta,
        ];
    }

    public function includeStatus(App $app): Primitive
    {
        return $this->primitive($app->status);
    }
}
