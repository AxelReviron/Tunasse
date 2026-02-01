<?php

namespace App\Models;

use App\Enums\RecurringTransactionUnit;
use App\Enums\TransactionType;
use App\Models\Scopes\OwnerScope;
use Carbon\Carbon;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
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
#[ScopedBy([OwnerScope::class])]
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

    /**
     * Calculate occurrences of this recurring transaction within a date range.
     *
     * @return int Number of occurrences in the given range
     */
    public function getOccurrencesInRange(Carbon $startRange, Carbon $endRange): int
    {
        if (! $this->is_recurring || ! $this->recurring_unit || ! $this->recurring_interval) {
            return 0;
        }

        $occurrences = 0;
        $currentOccurrence = Carbon::parse($this->date);

        // If the reference date is after the range, no occurrences
        if ($currentOccurrence->isAfter($endRange)) {
            return 0;
        }

        // Move forward to the first occurrence within or after the start range
        while ($currentOccurrence->isBefore($startRange)) {
            $currentOccurrence = $this->addInterval($currentOccurrence);
        }

        // Count all occurrences within the range
        while ($currentOccurrence->isBetween($startRange, $endRange, true)) {
            $occurrences++;
            $currentOccurrence = $this->addInterval($currentOccurrence);
        }

        return $occurrences;
    }

    /**
     * Add the recurring interval to a date.
     */
    private function addInterval(Carbon $date): Carbon
    {
        return match ($this->recurring_unit) {
            RecurringTransactionUnit::DAY => $date->copy()->addDays($this->recurring_interval),
            RecurringTransactionUnit::WEEK => $date->copy()->addWeeks($this->recurring_interval),
            RecurringTransactionUnit::MONTH => $date->copy()->addMonths($this->recurring_interval),
            RecurringTransactionUnit::YEAR => $date->copy()->addYears($this->recurring_interval),
        };
    }
}
