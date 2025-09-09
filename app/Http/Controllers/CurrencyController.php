<?php

namespace App\Http\Controllers;

use App\Http\Api\Controllers\CrudController;
use App\Http\Requests\Currency\SearchCurrencyRequest;
use App\Http\Requests\Currency\StoreCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;

class CurrencyController extends CrudController
{
    public function model(): string
    {
        return Currency::class;
    }

    public function resource(): string
    {
        return CurrencyResource::class;
    }

    public function formRequests(): array
    {
        return [
            'store' => StoreCurrencyRequest::class,
            'update' => UpdateCurrencyRequest::class,
            'search' => SearchCurrencyRequest::class,
        ];
    }

    public function relationships(): array
    {
        return [];
    }

    public function searchable(): array
    {
        return [
            'code',
            'name',
            'symbol',
        ];
    }
}
