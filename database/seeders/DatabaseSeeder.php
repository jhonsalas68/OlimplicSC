<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
        ]);

        // Crear usuario administrativo de prueba
        $admin = User::firstOrCreate([
            'email' => 'admin@olimpicsc.com',
        ], [
            'name' => 'Administrador',
            'last_name' => 'Principal',
            'username' => 'ADM01',
            'password' => Hash::make('adm123'),
            'is_active' => true,
        ]);
        $admin->assignRole('SuperAdmin');

        // Usuario de prueba adicional (opcional)
        User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('1234567'),
        ]);
    }
}
