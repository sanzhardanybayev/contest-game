<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuarterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quarter', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('first_player_id');
            $table->unsignedInteger('second_player_id');
            $table->unsignedInteger('winner_id');

            $table->foreign('first_player_id')->references('id')->on('teams');
            $table->foreign('second_player_id')->references('id')->on('teams');
            $table->foreign('winner_id')->references('id')->on('teams');
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
        Schema::dropIfExists('quarter');
    }
}
