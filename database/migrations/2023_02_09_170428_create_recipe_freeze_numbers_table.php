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
        Schema::create('recipe_freeze_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('full_number');
            $table->bigInteger('store_id');
            $table->bigInteger('order_sn');
            $table->dateTime('order_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipe_freeze_numbers');
    }
};
