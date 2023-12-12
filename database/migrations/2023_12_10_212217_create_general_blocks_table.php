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

            $table->string('stopPickup', 100)->nullable()->default('false');
            $table->string('stopDelivery', 100)->nullable()->default('false');
            $table->string('stopOrders', 100)->nullable()->default('false');

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
