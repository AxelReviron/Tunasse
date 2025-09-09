<?php

namespace App\Http\Controllers;

use App\Http\Api\Controllers\CrudController;
use App\Http\Requests\Account\SearchAccountRequest;
use App\Http\Requests\Account\StoreAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;

class AccountController extends CrudController
{

    public function model(): string
    {
        return Account::class;
    }

    public function resource(): string
    {
        return AccountResource::class;
    }

    public function relationships(): array
    {
        return [
            'currency',
            'user',
        ];
    }

    public function formRequests(): array
    {
        return [
            'store' => StoreAccountRequest::class,
            'update' => UpdateAccountRequest::class,
            'search' => SearchAccountRequest::class,
        ];
    }

    public function searchable(): array
    {
        return [
            'name',
            'type',
            'balance',
            'icon',
            'currency_id',
            'user_id',
        ];
    }
}
