<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MonthlyBill extends Model
{
    protected $fillable = [
        'customer_id',
        'billing_year',
        'billing_month',
        'amount',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(
            Payment::class,
            'monthly_bill_payment'
        )->withTimestamps();
    }

    public function scopeForPeriod(Builder $query, int $year, int $month): Builder
    {
        return $query
            ->where('billing_year', $year)
            ->where('billing_month', $month);
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        $trimmed = trim($keyword);

        if ($trimmed === '') {
            return $query;
        }

        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $trimmed);

        return $query->where(function (Builder $billQuery) use ($escaped) {
            $billQuery
                ->whereHas('customer', function (Builder $customerQuery) use ($escaped) {
                    $customerQuery->where(function (Builder $subQuery) use ($escaped) {
                        $subQuery->where('name', 'like', "%{$escaped}%")
                            ->orWhere('phone', 'like', "%{$escaped}%")
                            ->orWhere('user_name', 'like', "%{$escaped}%");
                    });
                })
                ->orWhereHas('customer.package', function (Builder $packageQuery) use ($escaped) {
                    $packageQuery->where('name', 'like', "%{$escaped}%");
                });
        });
    }
}
