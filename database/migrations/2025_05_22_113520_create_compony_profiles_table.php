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
        Schema::create('compony_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('business_name');
            $table->string('owner_name');
            $table->string('area_of_operation');
            $table->enum('service_offered', ['guarding','armed_respnse','mobile_patrol','event']);
            $table->string('coida_certificate')->nullable();
            $table->string('uif_certificate')->nullable();
            $table->string('psira_certificate')->nullable();
             $table->enum('enable_statutory_deductions', ['yes', 'no']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compony_profiles');
    }
};
