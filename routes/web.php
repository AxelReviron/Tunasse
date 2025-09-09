<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::prefix('api')->group(function () {
    Route::apiResource('accounts', AccountController::class);
    Route::prefix('accounts')->group(function () {
        Route::post('/search', [AccountController::class, 'search']);
        Route::post('/{id}/{relation}/{action}', [AccountController::class, 'relationAction']);
    });

    Route::apiResource('budgets', BudgetController::class);
    Route::prefix('budgets')->group(function () {
        Route::post('/search', [BudgetController::class, 'search']);
        Route::post('/{id}/{relation}/{action}', [BudgetController::class, 'relationAction']);
    });

    Route::apiResource('currencies', CurrencyController::class);
    Route::prefix('currencies')->group(function () {
        Route::post('/search', [CurrencyController::class, 'search']);
        Route::post('/{id}/{relation}/{action}', [CurrencyController::class, 'relationAction']);
    });

    Route::apiResource('transactions', TransactionController::class);
    Route::prefix('transactions')->group(function () {
        Route::post('/search', [TransactionController::class, 'search']);
        Route::post('/{id}/{relation}/{action}', [TransactionController::class, 'relationAction']);
    });

    Route::apiResource('users', UserController::class);
    Route::prefix('users')->group(function () {
        Route::post('/search', [UserController::class, 'search']);
        Route::post('/{id}/{relation}/{action}', [UserController::class, 'relationAction']);
    });
})->middleware(['auth']);
