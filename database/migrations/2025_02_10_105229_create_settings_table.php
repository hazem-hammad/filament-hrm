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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->nullable();
            $table->string('name')->nullable();
            $table->string('key');
            $table->text('value')->nullable();
            $table->boolean('is_configurable_by_admin')->default(true);
            $table->enum('type', ['string', 'text', 'boolean', 'numeric', 'file', 'date', 'time', 'color'])->nullable();
            $table->string('media_collection_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
