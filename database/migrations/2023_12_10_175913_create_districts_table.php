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
        Schema::create('districts', function (Blueprint $table) {
            $table->id();

            $table->string('serial', 255)->nullable();
            $table->string('name', 255);
            $table->string('nameAr', 255);

            // ::foreign keys
            $table->bigInteger('countryId')->unsigned()->nullable();
            $table->foreign('countryId')->references('id')->on('countries')->onDelete('cascade');

            $table->bigInteger('stateId')->unsigned()->nullable();
            $table->foreign('stateId')->references('id')->on('states')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
