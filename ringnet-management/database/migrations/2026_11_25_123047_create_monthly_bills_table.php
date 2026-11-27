<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_bills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->unsignedInteger('billing_month');
            $table->unsignedInteger('billing_year');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('status', 20)->default('UNPAID');
            $table->dateTime('paid_at')->nullable();

            $table->timestamps();

            $table->unique(['customer_id', 'billing_year', 'billing_month'], 'monthly_bills_customer_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_bills');
    }
};
