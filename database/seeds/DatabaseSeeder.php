<?php

use App\Role;
use App\User;
use App\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call(UserTableSeeder::class);
        DB::table('users')->delete();
        DB::table('roles')->delete();
        DB::table('role_user')->delete();
        DB::table('permissions')->delete();
        DB::table('permission_role')->delete();

        $users = array(
            ['name' => 'Ryan Chenkie', 'email' => 'ryanchenkie@gmail.com', 'login' => 'user1', 'password' => Hash::make('secret')],
            ['name' => 'Chris Sevilleja', 'email' => 'chris@scotch.io', 'login' => 'user2', 'password' => Hash::make('secret')],
            ['name' => 'Holly Lloyd', 'email' => 'holly@scotch.io', 'login' => 'user3', 'password' => Hash::make('secret')],
            ['name' => 'Adnan Kukic', 'email' => 'adnan@scotch.io', 'login' => 'user4', 'password' => Hash::make('secret')],
        );

        // Loop through each user above and create the record for them in the database
        foreach ($users as $user)
        {
            User::create($user);
        }

        $roles = array(
            ['name' => 'admin'],
            ['name' => 'user']
        );

        foreach ($roles as $role)
        {
            Role::create($role);
        }

        $adminUser = User::where('login', '=', 'user2')->first();
        $adminRole = Role::where('name', '=', 'admin')->first();
        DB::table('role_user')->insert(array('user_id' => $adminUser->id, 'role_id' => $adminRole->id));

        $normalUser = User::where('login', '=', 'user1')->first();
        $userRole = Role::where('name', '=', 'user')->first();
        DB::table('role_user')->insert(array('user_id' => $normalUser->id, 'role_id' => $userRole->id));

        $permissions = array(
            ['name' => 'manage-users'],
            ['name' => 'manage-routes']
        );
        foreach ($permissions as $permission)
        {
            Permission::create($permission);
        }


        $adminPermission = Permission::where('name', '=', $permissions[0]['name'])->first();
        $userPermission = Permission::where('name', '=', $permissions[1]['name'])->first();

        DB::table('permission_role')->insert(array('permission_id' => $adminPermission->id, 'role_id' => $adminRole->id));
        DB::table('permission_role')->insert(array('permission_id' => $userPermission->id, 'role_id' => $userRole->id));


        Model::reguard();
    }
}
