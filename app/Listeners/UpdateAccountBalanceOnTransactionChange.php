<?php

namespace App\Listeners;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Transaction;

class UpdateAccountBalanceOnTransactionChange
{
    /**
     * Handle transaction created event.
     */
    public function handleCreated(Transaction $transaction): void
    {
        $this->updateAccountBalance($transaction, 'add');
    }

    /**
     * Handle transaction updated event.
     */
    public function handleUpdated(Transaction $transaction): void
    {
        // Revert the old values
        $oldAmount = $transaction->getOriginal('amount');
        $oldType = $transaction->getOriginal('type');
        $oldAccountId = $transaction->getOriginal('account_id');

        // If account changed, update both accounts
        if ($oldAccountId !== $transaction->account_id) {
            // Revert from old account
            $oldAccount = Account::find($oldAccountId);
            if ($oldAccount) {
                $adjustment = $oldType === TransactionType::INCOME ? -$oldAmount : $oldAmount;
                $oldAccount->increment('balance', $adjustment);
            }

            // Add to new account
            $this->updateAccountBalance($transaction, 'add');
        } else {
            // Same account, calculate the difference
            $oldEffect = $oldType === TransactionType::INCOME ? $oldAmount : -$oldAmount;
            $newEffect = $transaction->type === TransactionType::INCOME ? $transaction->amount : -$transaction->amount;
            $difference = $newEffect - $oldEffect;

            if ($difference !== 0.0) {
                $transaction->account->increment('balance', $difference);
            }
        }
    }

    /**
     * Handle transaction deleted event (soft delete).
     */
    public function handleDeleted(Transaction $transaction): void
    {
        $this->updateAccountBalance($transaction, 'remove');
    }

    /**
     * Handle transaction restored event.
     */
    public function handleRestored(Transaction $transaction): void
    {
        $this->updateAccountBalance($transaction, 'add');
    }

    /**
     * Handle transaction force deleted event.
     */
    public function handleForceDeleted(Transaction $transaction): void
    {
        // Only remove if not already soft deleted
        if (! $transaction->trashed()) {
            $this->updateAccountBalance($transaction, 'remove');
        }
    }

    /**
     * Update account balance based on transaction.
     */
    private function updateAccountBalance(Transaction $transaction, string $operation): void
    {
        $account = $transaction->account;

        if (! $account) {
            return;
        }

        $amount = $transaction->amount;

        if ($operation === 'add') {
            $adjustment = $transaction->type === TransactionType::INCOME ? $amount : -$amount;
        } else {
            $adjustment = $transaction->type === TransactionType::INCOME ? -$amount : $amount;
        }

        $account->increment('balance', $adjustment);
    }
}
