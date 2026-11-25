<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends Model
{
    protected $fillable = [
        'customer_id',
        'paid_at',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function monthlyBills(): BelongsToMany
    {
        return $this->belongsToMany(
            MonthlyBill::class,
            'monthly_bill_payment'
        )->withTimestamps();
    }
}
