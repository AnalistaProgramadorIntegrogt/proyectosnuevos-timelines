<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $perm = Permission::firstOrCreate(['name' => 'manage-repository']);

        $admin = Role::where('name', 'super-admin')->first();
        if ($admin) {
            $admin->givePermissionTo($perm);
        }

        $mercadeo = Role::firstOrCreate(['name' => 'mercadeo']);
        $mercadeo->givePermissionTo($perm);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe down method
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $mercadeo = Role::where('name', 'mercadeo')->first();
        if ($mercadeo) {
            $mercadeo->delete();
        }
    }
};
