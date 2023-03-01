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
        Schema::create('certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('certificate_template_id');
            $table->uuid('product_id');
            $table->string('logo')->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('name_subtitle')->nullable();
            $table->string('course_name')->nullable();
            $table->boolean('show_signature')->default(false);
            $table->string('signature')->nullable();
            $table->boolean('show_seal')->default(false);
            $table->string('seal')->nullable();
            $table->boolean('show_completion_date')->default(false);
            $table->boolean('show_unique_serial_number')->default(false);
            $table->string('background_color')->nullable();
            $table->string('border_color')->nullable();
            $table->string('primary_text_color')->nullable();
            $table->string('secondary_text_color')->nullable();
            $table->string('template_color')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('certificate_template_id')->references('id')->on('certificate_templates');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificates');
    }
};
