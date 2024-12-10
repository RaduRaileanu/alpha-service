<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Define the permissions you want to create
     */
    private static $permissions = [
        'toggle active user',
        'manage service',
        'make appointment'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // create the permissions defined through the global variable
        foreach(self::$permissions as $permission){
            Permission::firstOrCreate(['name' => $permission]);
        }

        // create the admin role and assign to it the permission to change the active status on users
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(['toggle active user']);

        // create the service manager role and assign to it the permission to manage services
        $serviceManagerRole = Role::firstOrCreate(['name' => 'service_manager']);
        $serviceManagerRole->givePermissionTo(['manage service']);

        // create the client role and assign to it the permission to make appointments
        $clientRole = Role::firstOrCreate(['name' => 'client']);
        $clientRole->givePermissionTo(['make appointment']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // delete all the roles added through this migration
        Role::whereIn('name', ['admin', 'service_manager', 'client'])->delete();

        // delete all the permissions added through this migration
        Permission::whereIn('name', self::$permissions)->delete();
    }
};
