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
        Schema::create('address_infos', function (Blueprint $table) {
            $table->id();

            $table->text('address')->nullable();
            $table->text('longitude')->nullable();
            $table->text('latitude')->nullable();

            $table->text('image')->nullable();
            $table->boolean('isShown')->nullable()->default(1);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_infos');
    }
};
