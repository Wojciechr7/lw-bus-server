<?php

use App\Role;
use App\User;
use App\Permission;
use App\Day;
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

        $this->call([
            /*UsersSeeder::class,*/
            RolesSeeder::class,
            /*RoleUserSeeder::class,*/
            PermissionsSeeder::class,
            PermissionRoleSeeder::class,
            DaysSeeder::class
        ]);

    }
}
