<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property string $uuid
 * @property string $meta
 * @property int $status
 * @method active()
 */
class App extends Model
{
    protected $fillable = ['name', 'description', 'type', 'uuid', 'meta'];

    protected $casts = [
        'meta' => 'json'
    ];

    /**
     * @param Builder<App> $query
     * @return void
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 1);
    }
}
