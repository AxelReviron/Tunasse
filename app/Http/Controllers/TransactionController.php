<?php

namespace App\Http\Controllers;

use App\Http\Api\Controllers\CrudController;
use App\Http\Requests\Transaction\SearchTransactionRequest;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;

class TransactionController extends CrudController
{
    public function model(): string
    {
        return Transaction::class;
    }

    public function resource(): string
    {
        return TransactionResource::class;
    }

    public function formRequests(): array
    {
        return [
            'store' => StoreTransactionRequest::class,
            'update' => UpdateTransactionRequest::class,
            'search' => SearchTransactionRequest::class,
        ];
    }

    public function relationships(): array
    {
        return [
            'account',
            'user',
            'budget',
        ];
    }

    public function searchable(): array
    {
        return [
            'name',
            'description',
            'date',
            'is_recurring',
            'recurring_interval',
            'recurring_unit',
            'location',
            'amount',
            'type',
            'account_id',
            'user_id',
            'budget_id',
        ];
    }
}
