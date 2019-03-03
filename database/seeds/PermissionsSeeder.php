<?php

use App\Permission;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('permissions')->delete();

        $permissions = array(
            ['name' => 'manage-users'],
            ['name' => 'manage-routes']
        );
        foreach ($permissions as $permission)
        {
            Permission::create($permission);
        }

        Model::reguard();

    }
}
