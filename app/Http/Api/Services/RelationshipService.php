<?php

namespace App\Http\Api\Services;

use App\Http\Api\Contracts\CrudControllerContract;
use App\Http\Api\Traits\HasFindModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class RelationshipService
{//TODO: Test this
    use HasFindModel;

    private function handleBelongsToMany(BelongsToMany $relation, string $action, array $ids): void
    {
        match ($action) {
            'attach' => $relation->attach($ids),
            'detach' => $relation->detach($ids),
            'sync'   => $relation->sync($ids),
            default  => abort(400, "Invalid action <$action> for BelongsToMany"),
        };
    }

    private function handleBelongsTo(BelongsTo $relation, string $action, ?int $id): void
    {
        match ($action) {
            'associate'  => $relation->associate($id)->save(),
            'dissociate' => $relation->dissociate()->save(),
            default      => abort(400, "Invalid action <$action> for BelongsTo"),
        };
    }

    private function handleHasMany(HasMany $relation, string $action, array $ids): void
    {
        match ($action) {
            'sync' => $relation->whereIn('id', $ids)->update([$relation->getForeignKeyName() => $relation->getParentKey()]),
            default => abort(400, "Invalid action <$action> for HasMany"),
        };
    }

    public function handle(CrudControllerContract $controller, string|int $id, string $relation, string $action): JsonResource
    {
        $model = $this->findModel($controller, $id);
        Gate::authorize('update', $model);

        if (!method_exists($model, $relation) || !in_array($relation, $controller->relationships(), true)) {
            abort(404, "Relation <$relation> not defined");
        }

        $relationInstance = $model->$relation();

        // TODO: FormRequest ??
        $ids = request()->input('ids', []);
        $singleId = request()->input('id');

        match (true) {
            $relationInstance instanceof BelongsToMany => $this->handleBelongsToMany($relationInstance, $action, $ids),
            $relationInstance instanceof BelongsTo     => $this->handleBelongsTo($relationInstance, $action, $singleId),
            $relationInstance instanceof HasMany       => $this->handleHasMany($relationInstance, $action, $ids),
            default => abort(400, "Unsupported relation type"),
        };

        return $controller->resource()::collection($relationInstance->get());
    }

}
