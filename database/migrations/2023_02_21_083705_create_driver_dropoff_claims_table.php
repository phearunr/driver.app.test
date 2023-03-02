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
        Schema::create('dropoff_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scanouted_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->nullable()
                ->index();
            $table->timestamp('scanouted_at')->nullable();
            $table->foreignId('dropoffed_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->nullable()
                ->index();
            $table->timestamp('dropoffed_at')->nullable();
            $table->text('comments')->nullable();
            $table->bigInteger('status')->default(0)->comment(['scan out', 'drop-off']);
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
        Schema::dropIfExists('dropoff_claims');
    }
};
