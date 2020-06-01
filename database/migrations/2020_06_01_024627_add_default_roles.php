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
        $manage = Permission::create(['name' => 'manage users']);
        $invite = Permission::create(['name' => 'invite users']);

        $admin->givePermissionTo($manage);
        $admin->givePermissionTo($invite);

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
