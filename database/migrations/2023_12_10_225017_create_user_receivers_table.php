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
        Schema::create('user_receivers', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('phone');
            $table->string('phoneAlt')->nullable();


            // ::Address
            $table->text('address')->nullable();

            $table->bigInteger('stateId')->unsigned()->nullable();
            $table->foreign('stateId')->references('id')->on('states')->onDelete('set null');

            $table->bigInteger('districtId')->unsigned()->nullable();
            $table->foreign('districtId')->references('id')->on('districts')->onDelete('set null');

            $table->bigInteger('deliveryAreaId')->unsigned()->nullable();
            $table->foreign('deliveryAreaId')->references('id')->on('delivery_areas')->onDelete('set null');


            
            // ::Foreign keys
            $table->bigInteger('userId')->unsigned()->nullable();
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_receivers');
    }
};
