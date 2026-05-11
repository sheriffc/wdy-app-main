<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $items = [
        ['item_id' => 'mid_term_break',         'item_name' => 'Mid Term Break',            'display_order' => 1],
        ['item_id' => 'school_sport',           'item_name' => 'School Sport',              'display_order' => 2],
        ['item_id' => 'prizegiving_day',        'item_name' => 'Prizegiving Day',           'display_order' => 3],
        ['item_id' => 'inter_school_sport',     'item_name' => 'Inter-School Sport',        'display_order' => 4],
        ['item_id' => 'public_holiday',         'item_name' => 'Public Holiday',            'display_order' => 5],
        ['item_id' => 'end_of_first_term',      'item_name' => 'End of First Term',         'display_order' => 6],
        ['item_id' => 'end_of_second_term',     'item_name' => 'End of Second Term',        'display_order' => 7],
        ['item_id' => 'end_of_academic_year',   'item_name' => 'End of Academic Year',      'display_order' => 8],
        ['item_id' => 'public_health_emergency','item_name' => 'Public Health Emergency',   'display_order' => 9],
        ['item_id' => 'other_no_school',        'item_name' => 'Other (Specify)',           'display_order' => 10],
    ];

    public function up(): void
    {
        $now = now();
        $nextId = DB::table('option_list')->max('id') + 1;

        // Three list_names so that existing web JOIN filters (absent_reason, absent_reason_teacher)
        // continue to resolve the name without needing query changes.
        $lists = ['no_school_reason', 'absent_reason', 'absent_reason_teacher'];

        foreach ($lists as $listName) {
            foreach ($this->items as $item) {
                DB::table('option_list')->insertOrIgnore([
                    'id'            => $nextId++,
                    'list_name'     => $listName,
                    'item_id'       => $item['item_id'],
                    'item_name'     => $item['item_name'],
                    'item_extra'    => null,
                    'display_order' => $item['display_order'],
                    'active'        => 1,
                    'updated_at'    => $now,
                    'created_at'    => $now,
                    'synced_at'     => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $itemIds = array_column($this->items, 'item_id');
        DB::table('option_list')
            ->whereIn('list_name', ['no_school_reason', 'absent_reason', 'absent_reason_teacher'])
            ->whereIn('item_id', $itemIds)
            ->delete();
    }
};
