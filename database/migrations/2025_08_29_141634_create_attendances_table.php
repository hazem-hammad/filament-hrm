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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_plan_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in_time');
            $table->time('check_out_time')->nullable();
            $table->decimal('working_hours', 8, 2)->default(0);
            $table->decimal('missing_hours', 8, 2)->default(0);
            $table->integer('late_minutes')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_manual')->default(false);
            $table->timestamps();
            
            $table->unique(['employee_id', 'date']);
            $table->index(['date', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
