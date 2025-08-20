<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use App\Utils\CrudController;

class BudgetController extends CrudController
{
    protected function modelClass(): string
    {
        return Budget::class;
    }

    protected function resourceClass(): string
    {
        return BudgetResource::class;
    }
}
