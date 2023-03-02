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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('recipe_number');
            $table->bigInteger('store_id');
            $table->string('store_name');
            $table->bigInteger('buyer_id');
            $table->string('buyer_name');
            $table->string('bayer_mobile');
            $table->string('buyer_address');
            $table->bigInteger('order_sn');
            $table->dateTime('order_date');
            $table->bigInteger('order_state')->comment([10,20, 30, 35, 40]);
            $table->float('exchange_rate', 8, 2)->nullable();
            $table->bigInteger('exchange_rate_id')->nullable();
            $table->bigInteger('total_quantity');
            $table->float('delivery_fee', 8, 2)->default(0);
            $table->float('home_delivery_fee', 8, 2)->default(0);
            $table->float('grand_total_price', 8, 2);
            $table->float('grand_total_price_riel', 8, 2);
            $table->text('comment')->nullable();
            $table->bigInteger('downloaded')->default(0);
            $table->bigInteger('authorized_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['recipe_number', 'order_sn'])->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipes');
    }
};
