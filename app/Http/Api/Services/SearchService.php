<?php

namespace App\Http\Api\Services;

use App\Http\Api\Contracts\CrudControllerContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class SearchService
{

    /**
     * Apply filters to the query builder.
     *
     * @param CrudControllerContract $controller
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    private function applyFilters(CrudControllerContract $controller, Builder $query, array $filters): Builder
    {
        $searchableFields = $controller->searchable();

        foreach ($filters as $field => $operators) {
            if (!in_array($field, $searchableFields) || empty($operators))
            {
                continue;
            }

            if (is_array($operators)) {
                foreach ($operators as $operator => $value) {
                    switch ($operator) {
                        case 'eq':
                            $query->where($field, $value);
                            break;
                        case 'neq':
                            $query->whereNot($field, $value);
                            break;
                        case 'like':
                            $query->whereLike($field, $value);
                            break;
                        case 'gt':
                            $query->where($field, '>', $value);
                            break;
                        case 'gte':
                            $query->where($field, '>=', $value);
                            break;
                        case 'lt':
                            $query->where($field, '<', $value);
                            break;
                        case 'lte':
                            $query->where($field, '<=', $value);
                            break;
                        case 'in':
                            $query->whereIn($field, $value);
                            break;
                        case 'not_in':
                            $query->whereNotIn($field, $value);
                            break;
                        case 'between':
                            $query->whereBetween($field, $value);
                            break;
                    }
                }
            }
        }

        return $query;
    }

    /**
     * Apply sorting to the query builder.
     *
     * @param CrudControllerContract $controller
     * @param Builder $query
     * @param string|null $sort
     * @param string $direction
     * @return Builder
     */
    private function applySorting(CrudControllerContract $controller, Builder $query, ?string $sort, string $direction = 'asc'): Builder
    {
        if (!$sort) {
            return $query;
        }

        // Check if field is searchable
        if (!in_array($sort, $controller->searchable())) {
            return $query;
        }

        // Validate direction
        $direction = strtolower($direction);
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        return $query->orderBy($sort, $direction);
    }

    /**
     * Search and filter resources.
     *
     * @param CrudControllerContract $controller
     * @return JsonResource
     */
    public function search(CrudControllerContract $controller): JsonResource
    {
        $requestClass = $controller->formRequests()['search'];
        $request = resolve($requestClass);
        $modelClass = $controller->model();

        // Start query
        $query = $modelClass::query();

        // Apply filters
        $filters = $request->get('filters', []);
        if (is_array($filters)) {
            $query = $this->applyFilters($controller, $query, $filters);
        }

        // Sort
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');
        $query = $this->applySorting($controller, $query, $sort, $direction);

        // Execute query and check permissions
        $models = $query->get()->filter(function ($model) {
            return Gate::authorize('view', $model);
        });

        return $controller->resource()::collection($models);
        // TODO: Handle pagination
    }
}
