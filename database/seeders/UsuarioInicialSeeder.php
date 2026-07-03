<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioInicialSeeder extends Seeder
{
    /**
     * Crea el primer coordinador con una contraseña aleatoria que se
     * muestra UNA sola vez en consola. Nunca contraseñas fijas en código.
     */
    public function run(): void
    {
        $password = Str::password(16);

        User::firstOrCreate(
            ['email' => 'coordinador@ccbol.org'],
            [
                'name'     => 'Coordinador CCB',
                'password' => Hash::make($password), // bcrypt
                'rol'      => 'coordinador',
            ]
        );

        $this->command->warn("Contraseña inicial del coordinador (guárdela y cámbiela al primer ingreso): {$password}");
    }
}
