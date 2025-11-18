<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administracion\Usuario;
use App\Models\Administracion\Rol;
use Illuminate\Support\Facades\Hash;

class CreateDefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear rol Admin si no existe
        $adminRole = Rol::firstOrCreate([
            'nombre' => 'Administrador'
        ], [
            'descripcion' => 'Administrador del sistema',
            'activo' => true
        ]);

        // Crear usuario admin por defecto
        Usuario::firstOrCreate([
            'username' => 'admin'
        ], [
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'correo' => 'admin@ficct.edu.bo',
            'activo' => true,
            'id_rol' => $adminRole->id_rol
        ]);

        echo "✓ Usuario admin creado/verificado\n";
        echo "  Usuario: admin\n";
        echo "  Contraseña: admin123\n";
    }
}
