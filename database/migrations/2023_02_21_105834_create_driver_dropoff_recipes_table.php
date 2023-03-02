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
        Schema::create('dropoff_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dropoff_claim_id')
            ->references('id')
            ->on('dropoff_claims');
            $table->foreignId('recipe_id')
                ->references('id')
                ->on('recipes')
                ->cascadeOnDelete()
                ->index();
            $table->json('pinpoint')->nullable();
            $table->geometry('latitude')->nullable();
            $table->geometry('longitude')->nullable();
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
        Schema::dropIfExists('dropoff_recipes');
    }
};
