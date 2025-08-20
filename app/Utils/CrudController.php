<?php

namespace App\Utils;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

abstract class CrudController extends Controller
{
    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    /**
     * @return class-string<JsonResource>
     */
    abstract protected function resourceClass(): string;

    /**
     * @param string|int $id
     * @return Model
     */
    protected function findModel(string|int $id): Model
    {
        return $this->modelClass()::findOrFail($id);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        $modelClass = $this->modelClass();
        Gate::authorize('viewAny', $modelClass);

        $models = $modelClass::all();
        return $this->resourceClass()::collection($models);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FormRequest $request
     * @return JsonResource
     */
    public function store(FormRequest $request): JsonResource
    {
        $modelClass = $this->modelClass();
        Gate::authorize('create', $modelClass);

        $model = $modelClass::create($request->validated());

        return new ($this->resourceClass())($model);
    }

    /**
     * Display the specified resource.
     *
     * @param string|int $id
     * @return JsonResource
     */
    public function show(string|int $id): JsonResource
    {
        $model = $this->findModel($id);
        Gate::authorize('view', $model);

        return new ($this->resourceClass())($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FormRequest $request
     * @param string|int $id
     * @return JsonResource
     */
    public function update(FormRequest $request, string|int $id): JsonResource
    {
        $model = $this->findModel($id);
        Gate::authorize('update', $model);

        $model->update($request->validated());

        return new ($this->resourceClass())($model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string|int $id
     * @return JsonResponse
     */
    public function destroy(string|int $id): JsonResponse
    {
        $model = $this->findModel($id);
        Gate::authorize('delete', $model);

        $model->delete();

        return response()->json(null, 204);
    }
}
