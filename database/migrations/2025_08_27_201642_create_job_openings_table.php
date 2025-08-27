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
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->integer('number_of_positions')->default(1);
            $table->enum('work_type', ['full_time', 'part_time', 'contract', 'internship']);
            $table->enum('work_mode', ['remote', 'onsite', 'hybrid']);
            $table->enum('experience_level', ['entry', 'junior', 'mid', 'senior', 'lead']);
            $table->boolean('status')->default(true);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('short_description');
            $table->longText('long_description');
            $table->longText('job_requirements');
            $table->longText('benefits')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_openings');
    }
};
