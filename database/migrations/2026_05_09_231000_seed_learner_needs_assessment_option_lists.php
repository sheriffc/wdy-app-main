<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now    = now();
        $nextId = DB::table('option_list')->max('id') + 1;

        $rows = [
            // disability_severity — Washington Group Short Set scale
            ['list_name' => 'disability_severity', 'item_id' => '0_no_difficulty',    'item_name' => 'No Difficulty',        'display_order' => 1],
            ['list_name' => 'disability_severity', 'item_id' => '1_some_difficulty',   'item_name' => 'Some Difficulty',      'display_order' => 2],
            ['list_name' => 'disability_severity', 'item_id' => '2_lot_of_difficulty', 'item_name' => 'A Lot of Difficulty',  'display_order' => 3],
            ['list_name' => 'disability_severity', 'item_id' => '3_cannot_do',         'item_name' => 'Cannot Do At All',     'display_order' => 4],

            // disability_other_condition — common conditions tracked separately
            ['list_name' => 'disability_other_condition', 'item_id' => 'epilepsy',  'item_name' => 'Epilepsy',  'display_order' => 1],
            ['list_name' => 'disability_other_condition', 'item_id' => 'albinism',  'item_name' => 'Albinism',  'display_order' => 2],
            ['list_name' => 'disability_other_condition', 'item_id' => 'dwarfism',  'item_name' => 'Dwarfism',  'display_order' => 3],
        ];

        foreach ($rows as $row) {
            $inserted = DB::table('option_list')->insertOrIgnore([
                'id'            => $nextId,
                'list_name'     => $row['list_name'],
                'item_id'       => $row['item_id'],
                'item_name'     => $row['item_name'],
                'item_extra'    => null,
                'display_order' => $row['display_order'],
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
            ->whereIn('list_name', ['disability_severity', 'disability_other_condition'])
            ->delete();
    }
};
