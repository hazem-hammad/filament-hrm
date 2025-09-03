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
            // Marital status
            $table->string('marital_status')->after('gender');
            
            // Personal email
            $table->string('personal_email')->nullable()->after('email');
            
            // Business phone number
            $table->string('business_phone')->nullable()->after('phone');
            
            // National ID number
            $table->string('national_id')->after('employee_id');
            
            // Emergency contact fields
            $table->string('emergency_contact_name')->nullable()->after('address');
            $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_relation');
            
            // Contract type
            $table->string('contract_type')->after('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'marital_status',
                'personal_email',
                'business_phone',
                'national_id',
                'emergency_contact_name',
                'emergency_contact_relation',
                'emergency_contact_phone',
                'contract_type'
            ]);
        });
    }
};