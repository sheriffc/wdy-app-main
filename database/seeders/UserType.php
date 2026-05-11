<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_type')->insert(
        [
            ['type_id' => 20,   'type_name' => 'School Leader','display_order'=>20],
            ['type_id' => 40,   'type_name' => 'District Officer','display_order'=>40],
            ['type_id' => 60,   'type_name' => 'Administrator (view only)','display_order'=>60],
            ['type_id' => 80,   'type_name' => 'Administrator (full access)','display_order'=>80],
            ['type_id' => 999,  'type_name' => 'Super Administrator','display_order'=>999],
            ['type_id' => 0,    'type_name' => 'Public View','display_order'=>0]
        ]
        );
    }
}
