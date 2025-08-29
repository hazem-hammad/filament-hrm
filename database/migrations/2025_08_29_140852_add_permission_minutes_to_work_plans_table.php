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
        Schema::table('work_plans', function (Blueprint $table) {
            $table->integer('permission_minutes')->default(0)->after('working_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_plans', function (Blueprint $table) {
            $table->dropColumn('permission_minutes');
        });
    }
};
