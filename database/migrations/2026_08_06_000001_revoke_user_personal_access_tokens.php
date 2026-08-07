<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('personal_access_tokens')
            ->where('tokenable_type', User::class)
            ->delete();
    }

    public function down(): void
    {
        // Cutover irreversível: tokens por usuário não são restaurados.
    }
};
