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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('phoneAlt')->nullable();
            $table->text('password');
            
            $table->boolean('isActive')->nullable()->default(1);
            

            // :: Address Information
            $table->bigInteger('countryId')->unsigned()->nullable();
            $table->foreign('countryId')->references('id')->on('countries')->onDelete('set null');


            // 1: In SD
            $table->text('address')->nullable();

            $table->bigInteger('stateId')->unsigned()->nullable();
            $table->foreign('stateId')->references('id')->on('states')->onDelete('set null');

            $table->bigInteger('districtId')->unsigned()->nullable();
            $table->foreign('districtId')->references('id')->on('districts')->onDelete('set null');

            $table->bigInteger('deliveryAreaId')->unsigned()->nullable();
            $table->foreign('deliveryAreaId')->references('id')->on('delivery_areas')->onDelete('set null');

            // 2: In UK
            $table->string('townCity', 255)->nullable();
            $table->string('postcode', 255)->nullable(); 
            $table->text('firstAddressLine')->nullable(); //both
            $table->text('secAddressLine')->nullable(); //both
            $table->text('thirdAddressLine')->nullable();
            


            // 3: In IRL
            $table->string('postTown', 255)->nullable();
            $table->string('county', 255)->nullable();
            $table->string('eircode', 255)->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
