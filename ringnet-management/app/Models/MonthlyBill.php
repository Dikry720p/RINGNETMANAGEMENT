<?php

namespace App\Models;

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
}
