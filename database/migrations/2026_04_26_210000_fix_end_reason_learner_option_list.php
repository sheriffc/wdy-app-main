<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the bad row inserted with id=0 (missing id caused only the first row to insert)
        DB::table('option_list')
            ->where('id', 0)
            ->where('list_name', 'end_reason_learner')
            ->delete();

        $now = now();
        $nextId = DB::table('option_list')->max('id') + 1;

        $items = [
            ['item_id' => 'graduated',        'item_name' => 'Graduated',          'display_order' => 1],
            ['item_id' => 'transfer',          'item_name' => 'Transfer',           'display_order' => 2],
            ['item_id' => 'dropout_exams',     'item_name' => 'Dropout (Exams)',    'display_order' => 3],
            ['item_id' => 'dropout_maternal',  'item_name' => 'Dropout (Maternal)', 'display_order' => 4],
            ['item_id' => 'dropout_other',     'item_name' => 'Dropout (Other)',    'display_order' => 5],
            ['item_id' => 'unknown',           'item_name' => 'Unknown',            'display_order' => 6],
            ['item_id' => 'duplicate',         'item_name' => 'Duplicate',          'display_order' => 7],
            ['item_id' => 'mistake',           'item_name' => 'Mistake',            'display_order' => 8],
        ];

        foreach ($items as $item) {
            DB::table('option_list')->insertOrIgnore([
                'id'            => $nextId++,
                'list_name'     => 'end_reason_learner',
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

    public function down(): void
    {
        DB::table('option_list')
            ->where('list_name', 'end_reason_learner')
            ->delete();
    }
};
