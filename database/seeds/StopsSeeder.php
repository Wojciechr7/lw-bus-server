<?php

use App\Stop;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StopsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('stops')->delete();

        $stops = array(
            ['name' => 'Olsztyn'],
            ['name' => 'Lidzbark Warmiński'],
            ['name' => 'Dobre Miasto'],
            ['name' => 'Smolajny'],
            ['name' => 'Stary Dwór'],
            ['name' => 'Miłogórze'],
        );

        foreach ($stops as $stop)
        {
            Stop::create($stop);
        }

        Model::reguard();
    }
}
