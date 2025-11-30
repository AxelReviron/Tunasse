<?php

namespace App\Models;

use App\Enums\RecurringTransactionUnit;
use App\Enums\TransactionType;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $date
 * @property float $amount
 * @property TransactionType $type
 * @property RecurringTransactionUnit|null $recurring_unit
 * @property bool $is_recurring
 * @property int|null $recurring_interval
 */
class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recurring_unit' => RecurringTransactionUnit::class,
            'type' => TransactionType::class,
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function getRecurrenceLabelAttribute(): string
    {
        if (! $this->is_recurring || ! $this->recurring_unit) {
            return '-';
        }

        return "{$this->recurring_interval} {$this->recurring_unit->getLabel()}";
    }
}
