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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('reporting_to')->nullable()->after('department_id')->constrained('employees')->onDelete('set null');
            $table->index('reporting_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['reporting_to']);
            $table->dropIndex(['reporting_to']);
            $table->dropColumn('reporting_to');
        });
    }
};
