<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('pharmacies', function (Blueprint $table) {
        $table->softDeletes(); // Adds 'deleted_at' column
    });
}

public function down()
{
    Schema::table('pharmacies', function (Blueprint $table) {
        $table->dropSoftDeletes(); // Removes 'deleted_at' column
    });
}
};
