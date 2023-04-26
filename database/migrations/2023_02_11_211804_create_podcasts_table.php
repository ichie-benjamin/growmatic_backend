<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('podcasts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('podcast_category_id');
            $table->enum('type', ['private', 'public']);
            $table->string('title');
            $table->string('thumbnail')->nullable();
            $table->string('language');
            $table->boolean('in_episodic_order')->default(false);
            $table->boolean('in_serial_order')->default(false);
            $table->date('published_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('podcast_category_id')->references('id')->on('podcast_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('podcasts');
    }
};
