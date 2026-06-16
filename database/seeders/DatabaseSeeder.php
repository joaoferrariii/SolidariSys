<?php

namespace Database\Seeders;

use App\Models\Tipousuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Garante os perfis de acesso padrão
        Tipousuario::firstOrCreate(['id' => 1], ['nome' => 'Administrador']);
        Tipousuario::firstOrCreate(['id' => 2], ['nome' => 'Moderador']);
        Tipousuario::firstOrCreate(['id' => 3], ['nome' => 'Usuário Comum']);
    }
}