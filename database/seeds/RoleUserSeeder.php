<?php

use App\User;
use App\Role;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Model::unguard();

        DB::table('role_user')->delete();

        $adminUser = User::where('login', '=', 'user2')->first();
        $adminRole = Role::where('name', '=', 'admin')->first();
        DB::table('role_user')->insert(array('user_id' => $adminUser->id, 'role_id' => $adminRole->id));

        $normalUser = User::where('login', '=', 'user1')->first();
        $userRole = Role::where('name', '=', 'user')->first();
        DB::table('role_user')->insert(array('user_id' => $normalUser->id, 'role_id' => $userRole->id));

        Model::reguard();
    }
}
