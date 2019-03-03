<?php

use App\Role;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Model::unguard();

        DB::table('roles')->delete();

        $roles = array(
            ['name' => 'admin'],
            ['name' => 'user']
        );

        foreach ($roles as $role)
        {
            Role::create($role);
        }

        Model::reguard();

    }
}
