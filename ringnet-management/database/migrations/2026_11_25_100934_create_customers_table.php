
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->string('user_name', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();

            $table->date('activation_date')->nullable();

            $table->foreignId('package_id')
                ->nullable()
                ->constrained('packages')
                ->nullOnDelete();

            $table->decimal('billing_amount', 15, 2)->default(0);

            $table->text('sales')->nullable();

            $table->string('customer_status', 30)->default('AKTIF');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
