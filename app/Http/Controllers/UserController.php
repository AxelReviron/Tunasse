<?php

namespace App\Http\Controllers;

use App\Http\Api\Controllers\CrudController;
use App\Http\Requests\User\SearchUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends CrudController
{
    public function model(): string
    {
        return User::class;
    }

    public function resource(): string
    {
        return UserResource::class;
    }

    public function formRequests(): array
    {
        return [
            'store' => StoreUserRequest::class,
            'update' => UpdateUserRequest::class,
            'search' => SearchUserRequest::class,
        ];
    }

    public function relationships(): array
    {
        return [
            'accounts',
            'budgets',
            'transactions',
        ];
    }

    public function searchable(): array
    {
        return [
            'name',
            'email',
        ];
    }
}
