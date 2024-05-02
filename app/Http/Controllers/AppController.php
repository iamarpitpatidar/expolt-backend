<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAppRequest;
use App\Http\Requests\UpdateAppRequest;
use App\Models\App;
use App\Transformers\AppTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppController extends Controller
{
    public function index(): JsonResponse
    {
        $apps = App::query()->get();
        $apps = fractal($apps, new AppTransformer())->toArray();

        return $this->sendResponse($apps);
    }

    public function show(App $app): JsonResponse
    {
        $app = fractal($app, new AppTransformer())->parseIncludes(['uuid', 'status']);
        return $this->sendResponse($app->toArray());
    }

    public function store(CreateAppRequest $request): JsonResponse
    {
        $data = $request->validated();
        App::query()->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'type' => 'web',
            'uuid' => Str::uuid(),
            'meta' => [
                'background' => fake()->safeHexColor(),
                'redirectTo' => $data['meta']['redirectTo'] // @phpstan-ignore-line
            ]
        ]);
        return $this->sendResponse('App Created Successfully.');
    }

    public function update(UpdateAppRequest $request, App $app): JsonResponse
    {
        $data = $request->validated();
        $input = [
            'name' => $data['name'],
            'description' => $data['description'],
        ];

        if ($app->type === 'web') {
            $input['meta'] = array_merge($app['meta'], $data['meta']); //@phpstan-ignore-line
        }

        $app->update($input);
        return $this->sendResponse('App Updated Successfully.');
    }

    public function destroy(App $app): JsonResponse
    {
        $app->delete();
        return $this->sendResponse('App Deleted Successfully.');
    }

    public function listApps(): JsonResponse
    {
        $apps = App::active()->get();
        $apps = fractal($apps, new AppTransformer())->toArray();

        return $this->sendResponse($apps);
    }

    public function updateStatus(Request $request, App $app): JsonResponse
    {
        $status = $request->get('status');
        if (!is_int($status)) {
            return $this->sendErrorResponse('Invalid status');
        }

        $app->status = $status;
        $app->save();
        return $this->sendResponse('Updated status');
    }
}
