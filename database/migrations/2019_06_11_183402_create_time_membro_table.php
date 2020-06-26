<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeMembroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_membro', function (Blueprint $table) {
            $table->increments('id');           
            $table->integer('usuario_id')->unsigned();
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->integer('time_id')->unsigned();
            $table->foreign('time_id')->references('id')->on('time');
            $table->integer('nivel_id')->unsigned();
            $table->foreign('nivel_id')->references('id')->on('time_nivel');
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
        Schema::dropIfExists('time_membro');
    }
}
