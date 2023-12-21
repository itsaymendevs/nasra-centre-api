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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->text('userToken')->nullable();
            $table->integer('orderNumber')->nullable();
            $table->string('orderDateTime', 100)->nullable();
            
            // WAITING - COMPLETED - CANCELED
            $table->string('orderStatus', 100)->nullable();
            $table->string('orderSecondPhone', 100)->nullable();
            

            

        
            // ::receiving option (DELIVERY - PICKUP)
            $table->string('receivingOption', 100)->nullable();


            // country / lettersCode / toSDG
            $table->bigInteger('countryId')->unsigned()->nullable();
            $table->foreign('countryId')->references('id')->on('countries')->onDelete('cascade');
            $table->string('countryLettersCode', 100)->nullable();
            $table->double('toSDG', 10, 2)->nullable()->default(1);




            // :: 1: DELIVERY
            $table->bigInteger('stateId')->unsigned()->nullable();
            $table->foreign('stateId')->references('id')->on('states')->onDelete('cascade');
            $table->bigInteger('deliveryAreaId')->unsigned()->nullable();
            $table->foreign('deliveryAreaId')->references('id')->on('delivery_areas')->onDelete('cascade');

            $table->text('deliveryEstimatedTime')->nullable();
            $table->text('deliveryEstimatedTimeAr')->nullable();

            $table->double('deliveryPrice', 10, 2)->nullable();


            // :: 2: PICKUP
            $table->string('pickupCode', 100)->nullable();
            $table->bigInteger('storeId')->unsigned()->nullable();
            $table->foreign('storeId')->references('id')->on('pickup_stores')->onDelete('cascade');




            // ::total prices
            $table->double('productsPrice', 10, 2)->nullable();
            $table->double('orderTotalPrice', 10, 2)->nullable();


            // ::payments

            // DIRECTPAYMENT - ONLINEBANKINGPAYMENT - ATRECEIVINGPAYMENT
            $table->string('paymentType', 100)->nullable();
            $table->bigInteger('paymentId')->unsigned()->nullable();
            $table->foreign('paymentId')->references('id')->on('payments')->onDelete('cascade');
            $table->boolean('isPaymentDone')->nullable()->default(0);




            // ::GLOBAL ORDER - Receiver
            $table->string('receiverName', 255)->nullable();
            $table->string('receiverPhone', 100)->nullable();
            $table->string('receiverPhoneAlt', 100)->nullable();

            $table->bigInteger('receiverId')->unsigned()->nullable();
            $table->foreign('receiverId')->references('id')->on('user_receivers')->onDelete('cascade');




            // ::GLOBAL ORDER - Invoice
            $table->text('invoiceNumber')->nullable();
            $table->text('refundInvoiceNumber')->nullable();


            // ::foreign keys
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
        Schema::dropIfExists('orders');
    }
};