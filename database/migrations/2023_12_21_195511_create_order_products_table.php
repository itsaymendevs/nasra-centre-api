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


            $table->double('orderProductQuantity', 10, 2)->nullable();
            $table->double('orderProductPrice', 10, 2)->nullable();


            // ::foreign keys
            $table->bigInteger('productId')->unsigned()->nullable();
            $table->foreign('productId')->references('id')->on('products')->onDelete('set null');


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
