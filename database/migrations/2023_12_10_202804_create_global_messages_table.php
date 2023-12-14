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
        Schema::create('global_messages', function (Blueprint $table) {
            $table->id();

            $table->text('isFor')->nullable(); // delivery / pickup
            $table->text('type')->nullable(); // pending / processing etc
            $table->text('content')->nullable();
            $table->text('contentAr')->nullable();
            $table->string('target', 100)->nullable(); // customer or receiver

            $table->boolean('isActive')->nullable()->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_messages');
    }
};
