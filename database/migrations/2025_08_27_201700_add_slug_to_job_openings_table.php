<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_openings', function (Blueprint $table) {
            // Check if slug column doesn't exist before adding
            if (!Schema::hasColumn('job_openings', 'slug')) {
                $table->string('slug')->unique()->after('title')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_openings', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
