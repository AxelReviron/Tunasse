<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use App\Utils\HandlesPolicies;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class TransactionPolicy
{
    use HandlesPolicies;

    protected string $model = Transaction::class;

    protected function isOwn(User $user, Model $model): bool
    {
        return $model instanceof Transaction && $user->getKey() === $model->user->getKey();
    }
}
