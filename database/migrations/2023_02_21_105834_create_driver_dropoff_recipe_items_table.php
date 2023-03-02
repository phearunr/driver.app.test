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
        Schema::create('dropoff_recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dropoff_recipe_id')
                ->references('id')
                ->on('dropoff_recipes')
                ->cascadeOnDelete();
            $table->string('product_id');
            $table->string('product_name');
            $table->bigInteger('order_quantity')->default(0);
            $table->bigInteger('drop_off_quantity')->default(0);
            $table->bigInteger('drop_off_quantity_status')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dropoff_recipe_items');
    }
};
