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
        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('coordinates');
            $table->string('customer_mob')->nullable()->after('customer_id');
            $table->string('delivery_status')->nullable()->after('customer_mob');
            $table->string('delivered_coordinates')->nullable()->after('delivery_status');
            $table->string('otp')->nullable()->after('delivered_coordinates');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->dropColumn('customer_mob');
            $table->dropColumn('delivery_status');
            $table->dropColumn('delivered_coordinates');
            $table->dropColumn('otp');
        });
    }
};
