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
        Schema::create('document_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('document_folders')->onDelete('cascade');
            $table->string('color', 7)->default('#1976D2'); // Material blue
            $table->boolean('is_private')->default(false);
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['parent_id']);
            $table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_folders');
    }
};
