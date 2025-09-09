<?php

namespace App\Http\Api\Controllers;

use App\Http\Api\Contracts\CrudControllerContract;
use App\Http\Api\Services\CrudService;
use App\Http\Api\Services\RelationshipService;
use App\Http\Api\Services\SearchService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class CrudController extends Controller implements CrudControllerContract
{
    public function __construct(
        protected CrudService $crud,
        protected SearchService $search,
        protected RelationshipService $relationship
    ) {}

    public function index(): JsonResource
    {
        return $this->crud->index($this);
    }

    public function store(): JsonResource
    {
        return $this->crud->store($this);
    }

    public function show(string|int $id): JsonResource
    {
        return $this->crud->show($this, $id);
    }

    public function update(string|int $id): JsonResource
    {
        return $this->crud->update($this, $id);
    }

    public function destroy(string|int $id): JsonResponse
    {
        return $this->crud->destroy($this, $id);
    }

    public function search(): JsonResource
    {
        return $this->search->search($this);
    }

    public function relationAction(string|int $id, string $relation, string $action): JsonResource
    {
        return $this->relationship->handle($this, $id, $relation, $action);
    }
}
