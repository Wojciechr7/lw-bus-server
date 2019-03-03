<?php

use App\Permission;
use App\Role;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('permission_role')->delete();

        $permissions = array(
            ['name' => 'manage-users'],
            ['name' => 'manage-routes']
        );

        $adminPermission = Permission::where('name', '=', $permissions[0]['name'])->first();
        $userPermission = Permission::where('name', '=', $permissions[1]['name'])->first();
        $adminRole = Role::where('name', '=', 'admin')->first();
        $userRole = Role::where('name', '=', 'user')->first();

        DB::table('permission_role')->insert(array('permission_id' => $adminPermission->id, 'role_id' => $adminRole->id));
        DB::table('permission_role')->insert(array('permission_id' => $userPermission->id, 'role_id' => $userRole->id));

        Model::reguard();
    }
}
