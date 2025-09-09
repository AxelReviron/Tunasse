<?php

namespace App\Http\Api\Services;

use App\Http\Api\Contracts\CrudControllerContract;
use App\Http\Api\Traits\HasFindModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class CrudService
{
    use HasFindModel;

    /**
     * Display a listing of the resource.
     *
     * @param CrudControllerContract $controller
     * @return JsonResource
     */
    public function index(CrudControllerContract $controller): JsonResource
    {
        $modelClass = $controller->model();
        Gate::authorize('viewAny', $modelClass);

        $models = $modelClass::all();

        return $controller->resource()::collection($models);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CrudControllerContract $controller
     * @return JsonResource
     */
    public function store(CrudControllerContract $controller): JsonResource
    {
        $request = $controller->formRequests()['store'];
        $model = $controller->model()::create($request->validated());

        return new ($controller->resource())($model);
    }

    /**
     * Display the specified resource.
     *
     * @param CrudControllerContract $controller
     * @param string|int $id
     * @return JsonResource
     */
    public function show(CrudControllerContract $controller, string|int $id): JsonResource
    {
        $model = $this->findModel($controller, $id);
        Gate::authorize('view', $model);

        return new ($controller->resource())($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CrudControllerContract $controller
     * @param string|int $id
     * @return JsonResource
     */
    public function update(CrudControllerContract $controller, string|int $id): JsonResource
    {
        $model = $this->findModel($controller, $id);
        $request = $controller->formRequests()['update'];

        $model->update($request->validated());

        return new ($controller->resource())($model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CrudControllerContract $controller
     * @param string|int $id
     * @return JsonResponse
     */
    public function destroy(CrudControllerContract $controller, string|int $id): JsonResponse
    {
        $model = $this->findModel($controller, $id);
        Gate::authorize('delete', $model);

        $model->delete();

        return response()->json(null, 204);
    }
}
