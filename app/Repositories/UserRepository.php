<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritDoc}
     *
     * @return Collection<int, User>
     */
    public function all(): Collection
    {
        return $this->model->all();
    }
}
