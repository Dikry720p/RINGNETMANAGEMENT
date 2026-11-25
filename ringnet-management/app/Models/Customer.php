<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\MonthlyBill;
use App\Models\Payment;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'user_name',
        'phone',
        'address',
        'activation_date',
        'package_id',
        'billing_amount',
        'sales',
        'customer_status',
    ];

    protected function casts(): array
    {
        return [
            'activation_date' => 'date',
            'billing_amount' => 'decimal:2',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function monthlyBills(): HasMany
    {
        return $this->hasMany(MonthlyBill::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
