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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('folder_id')->nullable()->constrained('document_folders')->onDelete('cascade');
            $table->string('file_type', 50);
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->string('mime_type', 100);
            $table->boolean('is_private')->default(false);
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->json('metadata')->nullable(); // Additional file metadata
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
            
            $table->index(['folder_id']);
            $table->index(['assigned_to']);
            $table->index(['created_by']);
            $table->index(['file_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
