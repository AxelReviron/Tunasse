<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Account;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Account');
    }

    public function view(AuthUser $authUser, Account $account): bool
    {
        return $authUser->can('View:Account') && $this->isOwner($authUser, $account);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Account');
    }

    public function update(AuthUser $authUser, Account $account): bool
    {
        return $authUser->can('Update:Account') && $this->isOwner($authUser, $account);
    }

    public function delete(AuthUser $authUser, Account $account): bool
    {
        return $authUser->can('Delete:Account') && $this->isOwner($authUser, $account);
    }

    public function restore(AuthUser $authUser, Account $account): bool
    {
        return $authUser->can('Restore:Account') && $this->isOwner($authUser, $account);
    }

    public function forceDelete(AuthUser $authUser, Account $account): bool
    {
        return $authUser->can('ForceDelete:Account') && $this->isOwner($authUser, $account);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Account');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Account');
    }

    public function replicate(AuthUser $authUser, Account $account): bool
    {
        return $authUser->can('Replicate:Account') && $this->isOwner($authUser, $account);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Account');
    }

    protected function isOwner(AuthUser $authUser, Account $account): bool
    {
        return $account->user_id === $authUser->getKey();
    }
}
