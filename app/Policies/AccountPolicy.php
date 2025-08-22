<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\Budget;
use App\Models\User;
use App\Utils\HandlesPolicies;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class AccountPolicy
{
    use HandlesPolicies;

    protected string $model = Account::class;

    protected function isOwn(User $user, Model $model): bool
    {
        return $model instanceof Account && $user->getKey() === $model->user->getKey();
    }
}
