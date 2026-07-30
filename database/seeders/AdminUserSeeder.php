<?php

namespace Database\Seeders;

use App\Models\Time;
use App\Models\TimeMembro;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@rapidtask.local'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'email_verified_at' => now(),
                'status' => 0,
            ]
        );

        $time = Time::withoutGlobalScopes()->updateOrCreate(
            [
                'nome' => 'Time Admin',
                'usuario_id' => $admin->id,
            ],
            [
                'logo' => null,
            ]
        );

        TimeMembro::query()->updateOrCreate(
            [
                'time_id' => $time->id,
                'usuario_id' => $admin->id,
            ],
            [
                'nivel_id' => 1,
            ]
        );
    }
}
