<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Transaction;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TransactionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Transaction');
    }

    public function view(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('View:Transaction') && $this->isOwner($authUser, $transaction);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Transaction');
    }

    public function update(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('Update:Transaction') && $this->isOwner($authUser, $transaction);
    }

    public function delete(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('Delete:Transaction') && $this->isOwner($authUser, $transaction);
    }

    public function restore(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('Restore:Transaction') && $this->isOwner($authUser, $transaction);
    }

    public function forceDelete(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('ForceDelete:Transaction') && $this->isOwner($authUser, $transaction);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Transaction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Transaction');
    }

    public function replicate(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('Replicate:Transaction') && $this->isOwner($authUser, $transaction);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Transaction');
    }

    protected function isOwner(AuthUser $authUser, Transaction $transaction): bool
    {
        return $transaction->user_id === $authUser->getKey();
    }
}
