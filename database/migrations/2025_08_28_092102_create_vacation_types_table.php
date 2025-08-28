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
        Schema::create('vacation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('balance')->comment('Annual vacation balance in days');
            $table->integer('unlock_after_months')->default(0)->comment('Months after joining date to unlock this vacation type (0 = immediately)');
            $table->integer('required_days_before')->default(0)->comment('Required number of days before vacation request date');
            $table->boolean('requires_approval')->default(false)->comment('Whether this vacation type requires multiple level approval');
            $table->boolean('status')->default(true)->comment('Active/Inactive status');
            $table->text('description')->nullable()->comment('Description of the vacation type');
            $table->timestamps();

            $table->index(['status']);
            $table->index(['unlock_after_months']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacation_types');
    }
};
