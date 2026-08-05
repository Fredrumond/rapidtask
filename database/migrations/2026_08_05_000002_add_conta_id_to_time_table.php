<?php

use App\Support\ContaBackfill;
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
        Schema::table('time', function (Blueprint $table) {
            $table->foreignId('conta_id')
                ->nullable()
                ->constrained('conta');
        });

        ContaBackfill::run();

        Schema::table('time', function (Blueprint $table) {
            $table->unsignedBigInteger('conta_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time', function (Blueprint $table) {
            $table->dropConstrainedForeignId('conta_id');
        });
    }
};
