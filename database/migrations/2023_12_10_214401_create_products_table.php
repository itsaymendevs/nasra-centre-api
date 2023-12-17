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

            // ::unitId (null when byName is selected)
            $table->string('weightOption', 100)->nullable();
            $table->double('weight', 10,2)->nullable();
            $table->bigInteger('unitId')->unsigned()->nullable();
            $table->foreign('unitId')->references('id')->on('units')->onDelete('set null');


            $table->integer('units')->nullable()->default(0);
            $table->integer('quantityPerUnit')->nullable()->default(0);
            $table->integer('quantity')->nullable()->default(0);
            $table->integer('maxQuantityPerOrder')->nullable()->default(0);

            $table->boolean('isHidden')->nullable()->default(0);
            $table->boolean('isMainPage')->nullable()->default(0);


            // ::images + extras
            $table->text('image')->nullable();

            $table->text('firstExtraImage')->nullable();
            $table->text('secExtraImage')->nullable();
            $table->text('thirdExtraImage')->nullable();


            // ::foreign keys
            $table->bigInteger('companyId')->unsigned()->nullable();
            $table->foreign('companyId')->references('id')->on('companies')->onDelete('set null');

            $table->bigInteger('mainCategoryId')->unsigned()->nullable();
            $table->foreign('mainCategoryId')->references('id')->on('main_categories')->onDelete('cascade');

            $table->bigInteger('subCategoryId')->unsigned()->nullable();
            $table->foreign('subCategoryId')->references('id')->on('sub_categories')->onDelete('cascade');

            $table->bigInteger('typeId')->unsigned()->nullable();
            $table->foreign('typeId')->references('id')->on('types')->onDelete('cascade');


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
