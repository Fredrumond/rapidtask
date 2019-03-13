<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTarefasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tarefas', function($table)
        {
            $table->text('descricao')->nullable()->change();
            $table->date('dt_inicio')->nullable()->change();
            $table->date('dt_prevista')->nullable()->change();
            $table->date('dt_fim')->nullable()->change();
            $table->integer('tempo_estimado')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
