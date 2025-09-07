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
        Schema::table('q_r_codes', function (Blueprint $table) {
            // Design customization fields
            $table->integer('size')->default(300);
            $table->integer('margin')->default(1);
            $table->string('style')->default('square'); // square, circle, rounded
            $table->string('eye_style')->default('square'); // square, circle, rounded
            
            // Color customization
            $table->string('background_color')->default('#FFFFFF');
            $table->string('foreground_color')->default('#000000');
            $table->string('eye_color')->nullable();
            $table->boolean('gradient_enabled')->default(false);
            $table->string('gradient_start_color')->nullable();
            $table->string('gradient_end_color')->nullable();
            $table->string('gradient_type')->default('linear'); // linear, radial
            
            // Logo/Image customization
            $table->string('logo_path')->nullable();
            $table->integer('logo_size')->default(60); // Size in pixels
            $table->string('logo_position')->default('center'); // center, top, bottom
            $table->boolean('logo_background')->default(true);
            $table->string('logo_background_color')->default('#FFFFFF');
            
            // Advanced options
            $table->string('error_correction')->default('M'); // L, M, Q, H
            $table->string('encoding')->default('UTF-8');
            $table->json('custom_options')->nullable(); // For additional custom settings
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('q_r_codes', function (Blueprint $table) {
            $table->dropColumn([
                'size', 'margin', 'style', 'eye_style',
                'background_color', 'foreground_color', 'eye_color',
                'gradient_enabled', 'gradient_start_color', 'gradient_end_color', 'gradient_type',
                'logo_path', 'logo_size', 'logo_position', 'logo_background', 'logo_background_color',
                'error_correction', 'encoding', 'custom_options'
            ]);
        });
    }
};
