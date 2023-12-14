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
        Schema::create('general_blocks', function (Blueprint $table) {
            $table->id();

            $table->boolean('stopPickup')->nullable()->default(0);
            $table->boolean('stopDelivery')->nullable()->default(0);
            $table->boolean('stopOrders')->nullable()->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_blocks');
    }
};
