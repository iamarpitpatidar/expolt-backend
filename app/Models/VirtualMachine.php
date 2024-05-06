<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $uuid
 * @property string $current_state
 */
class VirtualMachine extends Model
{
    protected $fillable = [
        'uuid',
        'app_id',
        'user_id',
        'current_state',
        'meta'
    ];

    protected $casts = [
        'meta' => 'encrypted:json'
    ];
}
