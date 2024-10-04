<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $uuid
 * @property string $current_state
 * @property int $user_id
 */
class VirtualMachine extends Model
{
    protected $fillable = [
        'uuid',
        'app_id',
        'user_id',
        'current_state',
        'meta',
        'last_active'
    ];

    protected $casts = [
        'meta' => 'json'
    ];
}
