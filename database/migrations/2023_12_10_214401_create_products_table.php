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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('serial', 255)->nullable();
            $table->string('name', 255);
            $table->string('nameAr', 255);
            
            $table->integer('index')->nullable(); // :: sort related (category)
            $table->integer('indexMainPage')->nullable(); // :: sort related (app main-page)
            $table->integer('indexOffers')->nullable(); // :: sort related (sales + offers)


            $table->double('buyPrice', 10,2);
            $table->double('sellPrice', 10,2);
            $table->double('offerPrice', 10,2)->nullable();


            $table->text('desc')->nullable();
            $table->text('descAr')->nullable();

            // ::unit_id (null when byName is selected)
            $table->double('weight', 10,2)->nullable();
            $table->bigInteger('unit_id')->unsigned()->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');


            $table->integer('units')->nullable()->default(0);
            $table->integer('quantityPerUnit')->nullable()->default(0);
            $table->integer('quantity')->nullable()->default(0);
            $table->integer('maxQuantityPerOrder')->nullable()->default(0);

            $table->string('isHidden')->nullable()->default('false');
            $table->string('isMainPage')->nullable()->default('false');


            // ::images + extras
            $table->text('image')->nullable();

            $table->text('firstExtraImage')->nullable();
            $table->text('secExtraImage')->nullable();
            $table->text('thirdExtraImage')->nullable();


            // ::foreign keys
            $table->bigInteger('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');

            $table->bigInteger('maincategory_id')->unsigned()->nullable();
            $table->foreign('maincategory_id')->references('id')->on('maincategories')->onDelete('cascade');

            $table->bigInteger('subcategory_id')->unsigned()->nullable();
            $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('cascade');

            $table->bigInteger('type_id')->unsigned()->nullable();
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
