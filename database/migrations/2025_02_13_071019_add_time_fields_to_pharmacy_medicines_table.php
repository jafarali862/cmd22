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
        Schema::table('pharmacy_medicines', function (Blueprint $table) {
            $table->time('start_time_1')->nullable()->after('medicine_name');
            $table->time('end_time_1')->nullable()->after('start_time_1');
            $table->time('start_time_2')->nullable()->after('end_time_1');
            $table->time('end_time_2')->nullable()->after('start_time_2');
            $table->time('start_time_3')->nullable()->after('end_time_2');
            $table->time('end_time_3')->nullable()->after('start_time_3');
            $table->integer('req_unit')->nullable()->after('end_time_3');
            $table->integer('avail_unit')->nullable()->after('req_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacy_medicines', function (Blueprint $table) {
            $table->dropColumn([
                'start_time_1',
                'end_time_1',
                'start_time_2',
                'end_time_2',
                'start_time_3',
                'end_time_3',
                'req_unit',
                'avail_unit'

            ]);
        });
    }
};
