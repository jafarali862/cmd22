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
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('remember_token');
            $table->string('gender', 10)->nullable()->after('date_of_birth');
            $table->string('phone_number', 15)->nullable()->after('gender');
            $table->text('address')->nullable()->after('phone_number');
            $table->string('emergency_contact_name', 255)->nullable()->after('address');
            $table->string('emergency_contact_phone', 15)->nullable()->after('emergency_contact_name');
            $table->string('insurance_provider', 255)->nullable()->after('emergency_contact_phone');
            $table->string('insurance_policy_number', 255)->nullable()->after('insurance_provider');
            $table->string('primary_physician', 255)->nullable()->after('insurance_policy_number');
            $table->text('medical_history')->nullable()->after('primary_physician');
            $table->text('medications')->nullable()->after('medical_history');
            $table->text('allergies')->nullable()->after('medications');
            $table->string('blood_type', 10)->nullable()->after('allergies');
            $table->integer('status')->default(1)->after('blood_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'gender',
                'phone_number',
                'address',
                'emergency_contact_name',
                'emergency_contact_phone',
                'insurance_provider',
                'insurance_policy_number',
                'primary_physician',
                'medical_history',
                'medications',
                'allergies',
                'blood_type',
                'status',
            ]);
        });
    }
};
