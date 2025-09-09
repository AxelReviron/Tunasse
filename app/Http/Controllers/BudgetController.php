<?php

namespace App\Http\Controllers;

use App\Http\Api\Controllers\CrudController;
use App\Http\Requests\Account\SearchAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Http\Requests\Budget\StoreBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;

class BudgetController extends CrudController
{
    public function model(): string
    {
        return Budget::class;
    }

    public function resource(): string
    {
        return BudgetResource::class;
    }

    public function formRequests(): array
    {
        return [
            'store' => StoreBudgetRequest::class,
            'update' => UpdateAccountRequest::class,
            'search' => SearchAccountRequest::class,
        ];
    }

    public function relationships(): array
    {
        return [
            'user',
        ];
    }

    public function searchable(): array
    {
        return [
            'name',
            'amount',
            'start_date',
            'end_date',
            'icon',
            'user_id',
        ];
    }
}
