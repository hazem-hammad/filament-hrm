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
        Schema::create('job_custom_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_openings')->cascadeOnDelete();
            $table->foreignId('custom_question_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['job_id', 'custom_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_custom_questions');
    }
};
