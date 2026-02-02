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
        // getRawOriginal returns raw database value (minor units) without accessor transformation
        $oldAmountMinor = $transaction->getRawOriginal('amount');
        $oldType = $transaction->getOriginal('type');
        $oldAccountId = $transaction->getRawOriginal('account_id');

        // If account changed, update both accounts
        if ($oldAccountId !== $transaction->account_id) {
            // Revert from old account
            $oldAccount = Account::find($oldAccountId);
            if ($oldAccount) {
                $adjustment = $oldType === TransactionType::INCOME ? -$oldAmountMinor : $oldAmountMinor;
                $oldAccount->increment('balance', $adjustment);
            }

            // Add to new account
            $this->updateAccountBalance($transaction, 'add');
        } else {
            // Same account, calculate the difference (all in minor units)
            $newAmountMinor = $transaction->getAttributes()['amount'];
            $oldEffect = $oldType === TransactionType::INCOME ? $oldAmountMinor : -$oldAmountMinor;
            $newEffect = $transaction->type === TransactionType::INCOME ? $newAmountMinor : -$newAmountMinor;
            $difference = $newEffect - $oldEffect;

            if ($difference !== 0) {
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
     * Uses minor units for direct database operations.
     */
    private function updateAccountBalance(Transaction $transaction, string $operation): void
    {
        $account = $transaction->account;

        if (! $account) {
            return;
        }

        $amountMinor = $transaction->getAttributes()['amount'];

        if ($operation === 'add') {
            $adjustment = $transaction->type === TransactionType::INCOME ? $amountMinor : -$amountMinor;
        } else {
            $adjustment = $transaction->type === TransactionType::INCOME ? -$amountMinor : $amountMinor;
        }

        $account->increment('balance', $adjustment);
    }
}
