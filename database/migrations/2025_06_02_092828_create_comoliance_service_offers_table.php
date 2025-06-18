<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comoliance_service_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('compliance_id');
            $table->enum('security_grade', ['grade_a','grade_b','grade_c']);
            $table->enum('service_offered', ['guarding','armed_respnse','mobile_patrol','event']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comoliance_service_offers');
    }
};
