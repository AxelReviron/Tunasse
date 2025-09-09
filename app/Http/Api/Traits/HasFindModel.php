<?php

namespace App\Http\Api\Traits;

use App\Http\Api\Contracts\CrudControllerContract;
use Illuminate\Database\Eloquent\Model;

trait HasFindModel
{
    /**
     * Find a model by id.
     *
     * @param CrudControllerContract $controller
     * @param string|int $id
     * @return Model
     */
    private function findModel(CrudControllerContract $controller, string|int $id): Model
    {
        return $controller->model()::findOrFail($id);
    }
}
