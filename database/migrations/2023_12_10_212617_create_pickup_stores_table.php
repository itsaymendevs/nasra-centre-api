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
        Schema::create('pickup_stores', function (Blueprint $table) {
            $table->id();

            $table->string('serial', 255)->nullable();
            $table->string('title', 255);
            $table->string('titleAr', 255);
            $table->text('desc')->nullable();
            $table->text('descAr')->nullable();

            $table->text('receivingTimes')->nullable();
            $table->text('receivingTimesAr')->nullable();

            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();

            $table->boolean('isMainStore')->nullable()->default(0);
            $table->boolean('isActive')->nullable()->default(1);
            
            $table->text('image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_stores');
    }
};
