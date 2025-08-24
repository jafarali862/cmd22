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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('pres_id')->constrained('pharmacy_prescriptions');
      
            $table->foreign('pres_id')->references('id')->on('pharmacy_prescriptions')->onDelete('cascade');
            
            $table->string('status');
            $table->text('message');
            $table->string('trans_status')->nullable();
            $table->string('accno')->nullable();
            $table->string('amount')->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
