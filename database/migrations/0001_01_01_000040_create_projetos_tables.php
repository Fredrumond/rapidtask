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
        Schema::create('projetos', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('sigla');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('time_id')->constrained('time');
            $table->date('dt_inicio')->nullable();
            $table->date('dt_prevista')->nullable();
            $table->date('dt_fim')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('projetos_arquivos', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos');
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('nome');
            $table->text('descricao');
            $table->string('src');
            $table->timestamps();
        });

        Schema::create('projetos_anotacoes', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos');
            $table->foreignId('usuario_id')->constrained('users');
            $table->text('anotacao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projetos_anotacoes');
        Schema::dropIfExists('projetos_arquivos');
        Schema::dropIfExists('projetos');
    }
};
