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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('id_copy')->nullable();
            $table->string('coida_certificate')->nullable();
            $table->string('uif_certificate')->nullable();
            $table->string('psira_certificate')->nullable();
            $table->string('firearm_competency')->nullable();
            $table->string('statement_of_results')->nullable();
            $table->enum('status', ['pending', 'verified', 'declined'])->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_psira_certificates');
    }
};
