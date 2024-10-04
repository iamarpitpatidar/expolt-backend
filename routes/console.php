<?php

use App\Jobs\DestroyIdleMachines;
use Illuminate\Support\Facades\Schedule;

Schedule::command('sanctum:prune-expired --hours=24')->daily();
Schedule::command('auth:clear-resets')->everyFifteenMinutes();
Schedule::job(DestroyIdleMachines::class)->everyFiveMinutes();
