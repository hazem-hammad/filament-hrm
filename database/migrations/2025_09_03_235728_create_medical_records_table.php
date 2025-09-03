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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('insurance_status', ['n/a', 'pending', 'done'])->default('n/a');
            $table->string('insurance_number')->nullable();
            $table->enum('insurance_relation', ['child', 'spouse'])->nullable();
            $table->decimal('annual_cost', 10, 2)->nullable();
            $table->decimal('monthly_cost', 10, 2)->nullable();
            $table->date('activation_date')->nullable();
            $table->date('deactivation_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
