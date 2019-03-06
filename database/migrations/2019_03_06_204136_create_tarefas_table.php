<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTarefasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarefas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tipo_id')->unsigned();
            $table->foreign('tipo_id')->references('id')->on('tipos');
            $table->string('titulo');
            $table->text('descricao');
            $table->integer('situacao_id')->unsigned();
            $table->foreign('situacao_id')->references('id')->on('situacoes');
            $table->integer('prioridade_id')->unsigned();
            $table->foreign('prioridade_id')->references('id')->on('prioridades');
            $table->date('dt_inicio');
            $table->date('dt_prevista');
            $table->date('dt_fim');
            $table->integer('tempo_estimado');
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
        Schema::dropIfExists('tarefas');
    }
}
