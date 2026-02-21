<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$perms = ['view tahun-psms', 'create tahun-psms', 'edit tahun-psms', 'delete tahun-psms'];
foreach($perms as $p) {
    Permission::findOrCreate($p);
}
$admin = Role::findByName('admin');
$super = Role::findByName('super-admin');
$admin->givePermissionTo($perms);
$super->givePermissionTo($perms);

echo "Permissions added successfully.";
