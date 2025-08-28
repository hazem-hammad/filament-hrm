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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->enum('request_type', ['vacation', 'attendance'])->comment('Type of request');
            
            // Polymorphic relationship to vacation_types or attendance_types
            $table->unsignedBigInteger('requestable_id')->nullable();
            $table->string('requestable_type')->nullable();
            
            // Common fields
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('reason')->nullable()->comment('Reason for the request');
            $table->text('admin_notes')->nullable()->comment('Admin notes or rejection reason');
            
            // Vacation-specific fields
            $table->date('start_date')->nullable()->comment('Start date for vacation');
            $table->date('end_date')->nullable()->comment('End date for vacation');
            $table->integer('total_days')->nullable()->comment('Total vacation days requested');
            
            // Attendance-specific fields
            $table->date('request_date')->nullable()->comment('Date for attendance request');
            $table->decimal('hours', 5, 2)->nullable()->comment('Number of hours for attendance request');
            $table->time('start_time')->nullable()->comment('Start time for attendance');
            $table->time('end_time')->nullable()->comment('End time for attendance');
            
            // Approval workflow
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();

            $table->index(['employee_id', 'request_type']);
            $table->index(['status']);
            $table->index(['request_type']);
            $table->index(['requestable_id', 'requestable_type']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
