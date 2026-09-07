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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            // Nomor pasien unik (mis. running number)
            $table->string('patient_number')->unique();

            $table->string('name');
            $table->string('nik')->nullable()->unique();
            $table->string('gender', 20)->nullable();
            $table->date('birth_date')->nullable();

            $table->string('phone')->nullable();
            $table->string('address')->nullable();

            // patient_type: JEMAAT / UMUM
            $table->string('patient_type')->default('UMUM');
            $table->string('church_member_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
