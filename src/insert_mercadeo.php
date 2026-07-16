<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

$perm = Permission::firstOrCreate(['name' => 'manage-repository']);

$admin = Role::where('name', 'super-admin')->first();
if ($admin) $admin->givePermissionTo($perm);

$mercadeo = Role::firstOrCreate(['name' => 'mercadeo']);
$mercadeo->givePermissionTo($perm);

echo "Done\n";
