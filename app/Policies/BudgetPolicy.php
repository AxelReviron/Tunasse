<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\User;
use App\Utils\HandlesPolicies;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class BudgetPolicy
{
    use HandlesPolicies;

    protected string $model = Budget::class;

    protected function isOwn(User $user, Model $model): bool
    {
        return $model instanceof Budget && $user->getKey() === $model->user->getKey();
    }
}
