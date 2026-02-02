<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Models\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property float $balance Balance in major currency unit (stored as minor units in database)
 */
#[ScopedBy([OwnerScope::class])]
class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected function casts(): array
    {
        return [
            'type' => AccountType::class,
        ];
    }

    /**
     * Balance in major currency unit (converts from minor units stored in database).
     */
    protected function balance(): Attribute
    {
        return Attribute::make(
            get: function (int $value) {
                $decimals = $this->getDecimalPlaces();

                return $value / (10 ** $decimals);
            },
            set: function (float $value) {
                $decimals = $this->getDecimalPlaces();

                return (int) round($value * (10 ** $decimals));
            },
        );
    }

    /**
     * Get decimal places from currency, loading it if needed.
     */
    private function getDecimalPlaces(): int
    {
        if ($this->relationLoaded('currency')) {
            $decimals = $this->currency?->decimal_places;

            if ($decimals !== null) {
                return $decimals;
            }
        }

        if ($this->currency_id) {
            $currency = Currency::find($this->currency_id);
            $decimals = $currency?->decimal_places;

            if ($decimals !== null) {
                return $decimals;
            }
        }

        throw new \LogicException("Impossible to determine decimal : Account #{$this->account_id} has no currency associated.");
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
