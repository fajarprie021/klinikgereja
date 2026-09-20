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
        Schema::create('church_funds', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // INFLOW, OUTFLOW
            $table->string('category', 50)->default('OPERATIONAL'); // DONATION, SUBSIDY_DISBURSEMENT, OPERATIONAL, MEDICAL_SUPPLIES, OTHER
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->text('description');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('church_funds');
    }
};
