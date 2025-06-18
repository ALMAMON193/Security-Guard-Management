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
        Schema::create('add_guards', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('psira_number');
            $table->string('certificate_file');
            $table->string('wage_rate');
            $table->enum('rate_type', ['hourly', 'daily', 'monthly','yearly']);
            $table->string('area_of_operation');
            $table->string('controller_assignment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_guards');
    }
};
