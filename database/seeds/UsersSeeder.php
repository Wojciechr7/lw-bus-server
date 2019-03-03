<?php

use App\User;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Model::unguard();

        DB::table('users')->delete();

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

        Model::reguard();
    }
}
