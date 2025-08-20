<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use App\Utils\CrudController;

class CurrencyController extends CrudController
{
    protected function modelClass(): string
    {
        return Currency::class;
    }

    protected function resourceClass(): string
    {
        return CurrencyResource::class;
    }
}
