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
        Schema::create('delivery_areas', function (Blueprint $table) {
            $table->id();

            $table->string('serial', 255)->nullable();
            $table->string('name', 255);
            $table->string('nameAr', 255);
            $table->double('price', 10, 2);
            $table->boolean('isActive')->nullable()->default(1);


            // ::foreign keys
            $table->bigInteger('stateId')->unsigned()->nullable();
            $table->foreign('stateId')->references('id')->on('states')->onDelete('cascade');

            $table->bigInteger('districtId')->unsigned()->nullable();
            $table->foreign('districtId')->references('id')->on('districts')->onDelete('cascade');

            $table->bigInteger('deliveryTimeId')->unsigned()->nullable();
            $table->foreign('deliveryTimeId')->references('id')->on('delivery_times')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_areas');
    }
};
