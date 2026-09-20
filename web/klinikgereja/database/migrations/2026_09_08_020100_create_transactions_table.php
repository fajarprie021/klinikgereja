<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('medical_record_id')->nullable()->constrained('medical_records')->nullOnDelete();
            $table->decimal('service_cost', 12, 2)->default(0);
            $table->decimal('subsidy_amount', 12, 2)->default(0);
            $table->decimal('total_payment', 12, 2)->default(0);
            $table->string('payment_method', 30)->nullable(); // CASH, TRANSFER, SUBSIDY_FULL, OTHER
            $table->string('status', 20)->default('UNPAID'); // UNPAID, PAID, CANCELLED
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
