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
        Schema::create('types', function (Blueprint $table) {
            $table->id();

            $table->string('serial', 255)->nullable();
            $table->string('name', 255);
            $table->string('nameAr', 255);
            $table->integer('index'); // :: sort related

            // ::foreign keys
            $table->bigInteger('mainCategoryId')->unsigned()->nullable();
            $table->foreign('mainCategoryId')->references('id')->on('main_categories')->onDelete('cascade');

            $table->bigInteger('subCategoryId')->unsigned()->nullable();
            $table->foreign('subCategoryId')->references('id')->on('sub_categories')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types');
    }
};
