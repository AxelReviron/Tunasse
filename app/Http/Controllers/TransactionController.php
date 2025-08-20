<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Utils\CrudController;

class TransactionController extends CrudController
{
    protected function modelClass(): string
    {
        return Transaction::class;
    }

    protected function resourceClass(): string
    {
        return TransactionResource::class;
    }
}
