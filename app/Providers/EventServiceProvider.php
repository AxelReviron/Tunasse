<?php

namespace App\Providers;

use App\Listeners\UpdateAccountBalanceOnTransactionChange;
use App\Models\Transaction;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        #************************#
        #   Transaction events   #
        #************************#
        $listener = app(UpdateAccountBalanceOnTransactionChange::class);

        Transaction::created(fn (Transaction $transaction) => $listener->handleCreated($transaction));
        Transaction::updated(fn (Transaction $transaction) => $listener->handleUpdated($transaction));
        Transaction::deleted(fn (Transaction $transaction) => $listener->handleDeleted($transaction));
        Transaction::restored(fn (Transaction $transaction) => $listener->handleRestored($transaction));
        Transaction::forceDeleted(fn (Transaction $transaction) => $listener->handleForceDeleted($transaction));
    }
}
