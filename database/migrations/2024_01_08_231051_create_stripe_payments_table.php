<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('stripe_payments', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('orderId')->unsigned()->nullable();
            $table->foreign('orderId')->references('id')->on('orders')->onDelete('cascade');

            $table->text('paymentIntent')->nullable();
            $table->text('clientSecret')->nullable();
            $table->boolean('isPaymentDone')->nullable()->default(1);



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('stripe_payments');
    }
};
