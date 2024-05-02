<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = Settings::query()->pluck('value', 'key')->all();
        return $this->sendResponse(['settings' => $settings]);
    }

    public function update(Request $request): JsonResponse
    {
        $settings = $request->all();
        $allowed = ['idle_timout'];
        foreach ($settings as $key => $value) {
            $setting = Settings::query()->where('key', $key)->first();
            if (isset($allowed[$key]) && $setting) {
                $setting->update(['value' => $value]);
            }
        }

        return $this->sendResponse('Settings updated successfully');
    }
}
