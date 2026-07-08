<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Proyectos',
                'email' => 'admin@integro.net.gt',
                'password' => Hash::make('Admin2026!'),
                'role' => 'super-admin',
            ],
            [
                'name' => 'Usuario Proyectos',
                'email' => 'usuario@integro.net.gt',
                'password' => Hash::make('Usuario2026!'),
                'role' => 'user',
            ],
            [
                'name' => 'Invitado Proyectos',
                'email' => 'invitado@integro.net.gt',
                'password' => Hash::make('Invitado2026!'),
                'role' => 'user',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
            $user->assignRole($role);
        }
    }
}
