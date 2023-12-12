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
        Schema::create('media_infos', function (Blueprint $table) {
            $table->id();

            $table->text('websiteURL')->nullable();
            
            $table->text('facebookID')->nullable();
            $table->text('facebookURL')->nullable();
            $table->text('linkedinID')->nullable();
            $table->text('linkedinURL')->nullable();
            $table->text('twitterID')->nullable();
            $table->text('twitterURL')->nullable();
            $table->text('instagramID')->nullable();
            $table->text('instagramURL')->nullable();

            $table->string('videoTitle', 255)->nullable();
            $table->string('videoTitleAr', 255)->nullable();
            $table->text('videoURL')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_infos');
    }
};
