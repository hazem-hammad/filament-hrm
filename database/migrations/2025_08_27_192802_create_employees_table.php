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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            
            // Personal Details
            $table->string('name');
            $table->string('phone');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('email')->unique();
            $table->text('address')->nullable();
            $table->string('password');
            
            // Company Details
            $table->string('employee_id')->unique();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->date('company_date_of_joining');
            $table->boolean('status')->default(true);
            $table->timestamp('password_set_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
