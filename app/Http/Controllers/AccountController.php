<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Utils\CrudController;

class AccountController extends CrudController
{
    protected function modelClass(): string
    {
        return Account::class;
    }

    protected function resourceClass(): string
    {
        return AccountResource::class;
    }
}
