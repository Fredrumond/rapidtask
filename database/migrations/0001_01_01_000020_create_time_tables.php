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
        Schema::create('time', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->string('nome');
            $table->string('logo')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('time_membro', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->foreignId('time_id')->constrained('time');
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('nivel_id')->constrained('time_nivel');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['time_id', 'usuario_id']);
        });

        Schema::create('membro_time_convite', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->string('nome');
            $table->string('email');
            $table->foreignId('time_id')->constrained('time');
            $table->string('token')->nullable();
            $table->integer('status');
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membro_time_convite');
        Schema::dropIfExists('time_membro');
        Schema::dropIfExists('time');
    }
};
