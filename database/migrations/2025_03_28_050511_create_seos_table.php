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
        Schema::create('seos', function (Blueprint $table) {
            $table->id();
            //- readble bt admin
            $table->string('name');

            //- unique key
            $table->string('key')->unique()->index();

            //- whether if this seo is for page or a single data record
            $table->enum('type', ['page', 'record']);

            $table->string('title');
            $table->string('description');
            $table->string('keywords');
            $table->string('robots_meta');
            $table->string('canonical_url');
            $table->string('og_title');
            $table->string('og_description');
            $table->string('og_image');
            $table->string('og_image_alt');
            $table->string('og_locale');
            $table->string('og_site_name');
            $table->string('twitter_title');
            $table->string('twitter_description');
            $table->string('twitter_image');
            $table->string('twitter_alt');
            $table->string('custom_meta_tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seos');
    }
};
