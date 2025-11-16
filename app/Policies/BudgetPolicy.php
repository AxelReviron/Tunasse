<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Account;
use App\Models\Budget;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class BudgetPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Budget');
    }

    public function view(AuthUser $authUser, Budget $budget): bool
    {
        return $authUser->can('View:Budget') && $this->isOwner($authUser, $budget);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Budget');
    }

    public function update(AuthUser $authUser, Budget $budget): bool
    {
        return $authUser->can('Update:Budget') && $this->isOwner($authUser, $budget);
    }

    public function delete(AuthUser $authUser, Budget $budget): bool
    {
        return $authUser->can('Delete:Budget') && $this->isOwner($authUser, $budget);
    }

    public function restore(AuthUser $authUser, Budget $budget): bool
    {
        return $authUser->can('Restore:Budget') && $this->isOwner($authUser, $budget);
    }

    public function forceDelete(AuthUser $authUser, Budget $budget): bool
    {
        return $authUser->can('ForceDelete:Budget') && $this->isOwner($authUser, $budget);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Budget');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Budget');
    }

    public function replicate(AuthUser $authUser, Budget $budget): bool
    {
        return $authUser->can('Replicate:Budget') && $this->isOwner($authUser, $budget);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Budget');
    }

    protected function isOwner(AuthUser $authUser, Budget $budget): bool
    {
        return $budget->user_id === $authUser->getKey();
    }
}
