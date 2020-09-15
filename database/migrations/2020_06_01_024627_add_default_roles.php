<?php

use Illuminate\Database\Migrations\Migration;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddDefaultRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $admin = Role::create(['name' => 'admin']);

        $defaultAdminPermissions = ['manage users', 'list users', 'invite users', 'list roles'];

        foreach ($defaultAdminPermissions as $permissionName) {
            $permission = Permission::create(['name' => $permissionName]);
            $admin->givePermissionTo($permission);
        }

        Role::create(['name' => 'user']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
