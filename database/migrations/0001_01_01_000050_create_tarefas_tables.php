<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tarefas', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->foreignId('situacao_id')->constrained('situacoes');
            $table->foreignId('prioridade_id')->constrained('prioridades');
            $table->date('dt_inicio')->nullable();
            $table->date('dt_prevista')->nullable();
            $table->date('dt_fim')->nullable();
            $table->integer('tempo_estimado')->nullable();
            $table->integer('status')->default(0);
            $table->foreignId('projeto_id')->constrained('projetos');
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });

        Schema::create('tarefa_comentario', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->foreignId('tarefa_id')->constrained('tarefas');
            $table->foreignId('usuario_id')->constrained('users');
            $table->text('comentario');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tarefa_historico', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->foreignId('tarefa_id')->constrained('tarefas');
            $table->foreignId('usuario_id')->constrained('users');
            $table->text('atividade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarefa_historico');
        Schema::dropIfExists('tarefa_comentario');
        Schema::dropIfExists('tarefas');
    }
};
