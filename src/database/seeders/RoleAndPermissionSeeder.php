<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create global permissions
        Permission::create(['name' => 'manage-users']);
        Permission::create(['name' => 'manage-templates']);
        Permission::create(['name' => 'manage-settings']);
        Permission::create(['name' => 'view-all-projects']);
        Permission::create(['name' => 'manage-repository']);
        
        // Create roles and assign permissions
        $admin = Role::create(['name' => 'super-admin']);
        $admin->givePermissionTo(Permission::all());
        
        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo([]);

        $mercadeo = Role::create(['name' => 'mercadeo']);
        $mercadeo->givePermissionTo(['manage-repository']);
    }
}
