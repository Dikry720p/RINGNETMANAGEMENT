
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_bill_payment', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_id')
                ->constrained('payments');

            $table->foreignId('monthly_bill_id')
                ->constrained('monthly_bills')
                ->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_bill_payment');
    }
};
