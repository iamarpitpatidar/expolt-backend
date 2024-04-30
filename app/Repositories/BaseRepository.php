<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-template TModelClass of Model
 */
class BaseRepository
{
    /** @phpstan-var TModelClass $model */
    protected Model $model;

    /**
     * @phpstan-param TModelClass $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @phpstan-return TModelClass
     */
    public function create(array $attributes): Model
    {
        /** @phpstan-ignore-next-line */
        return $this->model->create($attributes);
    }

    /**
     * @phpstan-return TModelClass
     */
    public function find(int $id): ?Model
    {
        /** @phpstan-ignore-next-line */
        return $this->model->find($id);
    }
}
