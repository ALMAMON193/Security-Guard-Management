<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('business_name', 255)->nullable()->change();
            $table->string('owner_name', 255)->nullable()->change();
            $table->string('area_of_operation', 255)->nullable()->change();
            $table->boolean('enable_statutory_deductions')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('business_name', 255)->nullable(false)->change();
            $table->string('owner_name', 255)->nullable(false)->change();
            $table->string('area_of_operation', 255)->nullable(false)->change();
            $table->boolean('enable_statutory_deductions')->nullable(false)->change();
        });
    }
};
