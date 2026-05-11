<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $levels = [
            // Pre-school
            ['item_id' => 'kg1',   'item_name' => 'KG 1',    'item_extra' => '1',  'display_order' => 1],
            ['item_id' => 'kg2',   'item_name' => 'KG 2',    'item_extra' => '2',  'display_order' => 2],
            // Primary
            ['item_id' => 'p1',    'item_name' => 'P 1',     'item_extra' => '3',  'display_order' => 3],
            ['item_id' => 'p2',    'item_name' => 'P 2',     'item_extra' => '4',  'display_order' => 4],
            ['item_id' => 'p3',    'item_name' => 'P 3',     'item_extra' => '5',  'display_order' => 5],
            ['item_id' => 'p4',    'item_name' => 'P 4',     'item_extra' => '6',  'display_order' => 6],
            ['item_id' => 'p5',    'item_name' => 'P 5',     'item_extra' => '7',  'display_order' => 7],
            ['item_id' => 'p6',    'item_name' => 'P 6',     'item_extra' => '8',  'display_order' => 8],
            // Junior Secondary School
            ['item_id' => 'jss1',  'item_name' => 'JSS 1',   'item_extra' => '9',  'display_order' => 9],
            ['item_id' => 'jss2',  'item_name' => 'JSS 2',   'item_extra' => '10', 'display_order' => 10],
            ['item_id' => 'jss3',  'item_name' => 'JSS 3',   'item_extra' => '11', 'display_order' => 11],
            // Senior Secondary School
            ['item_id' => 'sss1',  'item_name' => 'SSS 1',   'item_extra' => '12', 'display_order' => 12],
            ['item_id' => 'sss2',  'item_name' => 'SSS 2',   'item_extra' => '13', 'display_order' => 13],
            ['item_id' => 'sss3',  'item_name' => 'SSS 3',   'item_extra' => '14', 'display_order' => 14],
        ];

        $nextId = DB::table('option_list')->max('id') + 1;

        foreach ($levels as $level) {
            $inserted = DB::table('option_list')->insertOrIgnore([
                'id'            => $nextId,
                'list_name'     => 'school_group_level',
                'item_id'       => $level['item_id'],
                'item_name'     => $level['item_name'],
                'item_extra'    => $level['item_extra'],
                'display_order' => $level['display_order'],
                'active'        => 1,
                'updated_at'    => $now,
                'created_at'    => $now,
                'synced_at'     => $now,
            ]);
            if ($inserted) $nextId++;
        }
    }

    public function down(): void
    {
        DB::table('option_list')
            ->where('list_name', 'school_group_level')
            ->delete();
    }
};
