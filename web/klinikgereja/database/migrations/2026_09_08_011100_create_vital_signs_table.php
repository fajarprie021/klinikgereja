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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
            $table->string('blood_pressure', 20)->nullable(); // e.g. 120/80 mmHg
            $table->decimal('temperature', 4, 1)->nullable(); // e.g. 36.5 Celcius
            $table->decimal('weight', 5, 2)->nullable(); // e.g. 65.50 kg
            $table->decimal('height', 5, 2)->nullable(); // e.g. 170.00 cm
            $table->integer('heart_rate')->nullable(); // bpm
            $table->integer('respiratory_rate')->nullable(); // breaths per minute
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
