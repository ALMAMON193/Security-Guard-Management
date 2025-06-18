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
        Schema::create('quote_builders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quote_id');
            $table->string('service_type');
            $table->string('site_location');
            $table->enum('guard_grade', ['grade_a','grade_b','grade_c']);
            $table->string('cost');
            $table->string('margin')->comment('%');
            $table->string('total_cost');
            $table->string('total_margin')->comment('%');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_builders');
    }
};
