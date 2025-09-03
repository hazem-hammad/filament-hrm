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
        Schema::table('job_stages', function (Blueprint $table) {
            $table->boolean('sending_email')->default(true)->after('email_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_stages', function (Blueprint $table) {
            $table->dropColumn('sending_email');
        });
    }
};