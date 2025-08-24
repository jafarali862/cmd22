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
        Schema::create('clinic_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->json('prescription'); 
            $table->json('test')->nullable(); 
            $table->unsignedBigInteger('user_id'); 
            $table->text('address'); 
            $table->unsignedBigInteger('clinic_id'); 
            $table->string('lat_long'); 
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void 
    {
        Schema::dropIfExists('clinic_prescriptions');
    }
};
