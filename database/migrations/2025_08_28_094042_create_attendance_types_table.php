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
        Schema::create('attendance_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('has_limit')->default(false)->comment('Whether this attendance type has limits');
            $table->integer('max_hours_per_month')->nullable()->comment('Maximum hours allowed per month');
            $table->integer('max_requests_per_month')->nullable()->comment('Maximum number of requests per month');
            $table->decimal('max_hours_per_request', 5, 2)->nullable()->comment('Maximum hours per single request');
            $table->boolean('requires_approval')->default(false)->comment('Whether this attendance type requires multiple level approval');
            $table->boolean('status')->default(true)->comment('Active/Inactive status');
            $table->text('description')->nullable()->comment('Description of the attendance type');
            $table->timestamps();

            $table->index(['status']);
            $table->index(['has_limit']);
            $table->index(['requires_approval']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_types');
    }
};
