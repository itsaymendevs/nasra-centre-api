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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();


            
            // General
            $table->double('orderProductQuantity', 10, 2)->nullable();
            $table->double('orderProductPrice', 10, 2)->nullable();


            // ::Product
            $table->bigInteger('productId')->unsigned()->nullable();
            $table->foreign('productId')->references('id')->on('products')->onDelete('set null');

            $table->string('serial', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('nameAr', 255)->nullable();
            $table->double('sellPrice', 10,2)->nullable();



            // ::Foreign Keys
            $table->bigInteger('orderId')->unsigned()->nullable();
            $table->foreign('orderId')->references('id')->on('orders')->onDelete('cascade');



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
        Schema::dropIfExists('order_products');
    }
};
