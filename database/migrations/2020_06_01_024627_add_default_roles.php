<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\User;

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

        $defaultAdminPermissions = ['manage users', 'list users', 'list roles'];

        foreach ($defaultAdminPermissions as $permissionName) {
            $permission = Permission::create(['name' => $permissionName]);
            $admin->givePermissionTo($permission);
        }

        Role::create(['name' => 'user']);

        $users = User::all();

        foreach ($users as $user) {
            $user->assignRole('admin');
        }
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
