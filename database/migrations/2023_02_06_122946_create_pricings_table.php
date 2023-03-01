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
        Schema::create('pricings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuidMorphs('priceable');
            $table->string('amount');
            $table->enum('type', ['recurring', 'one-time', 'free'])->nullable();
            $table->enum('recurrences', ['weekly', 'monthly', 'yearly'])->nullable();
            $table->string('recurring_amount')->nullable();
            $table->string('payment_plan')->nullable();
            $table->string('payment_plan_description')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pricings');
    }
};
