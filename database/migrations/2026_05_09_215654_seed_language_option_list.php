<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now    = now();
        $nextId = DB::table('option_list')->max('id') + 1;

        $languages = [
            ['item_id' => 'krio',       'item_name' => 'Krio',      'display_order' => 1],
            ['item_id' => 'temne',      'item_name' => 'Temne',     'display_order' => 2],
            ['item_id' => 'mende',      'item_name' => 'Mende',     'display_order' => 3],
            ['item_id' => 'limba',      'item_name' => 'Limba',     'display_order' => 4],
            ['item_id' => 'kono',       'item_name' => 'Kono',      'display_order' => 5],
            ['item_id' => 'kuranko',    'item_name' => 'Kuranko',   'display_order' => 6],
            ['item_id' => 'fula',       'item_name' => 'Fula',      'display_order' => 7],
            ['item_id' => 'mandingo',   'item_name' => 'Mandingo',  'display_order' => 8],
            ['item_id' => 'susu',       'item_name' => 'Susu',      'display_order' => 9],
            ['item_id' => 'sherbro',    'item_name' => 'Sherbro',   'display_order' => 10],
            ['item_id' => 'loko',       'item_name' => 'Loko',      'display_order' => 11],
            ['item_id' => 'kissi',      'item_name' => 'Kissi',     'display_order' => 12],
            ['item_id' => 'vai',        'item_name' => 'Vai',       'display_order' => 13],
            ['item_id' => 'gola',       'item_name' => 'Gola',      'display_order' => 14],
            ['item_id' => 'english',    'item_name' => 'English',   'display_order' => 15],
            ['item_id' => 'other',      'item_name' => 'Other',     'display_order' => 16],
        ];

        foreach ($languages as $lang) {
            $inserted = DB::table('option_list')->insertOrIgnore([
                'id'            => $nextId,
                'list_name'     => 'language',
                'item_id'       => $lang['item_id'],
                'item_name'     => $lang['item_name'],
                'item_extra'    => null,
                'display_order' => $lang['display_order'],
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
            ->where('list_name', 'language')
            ->delete();
    }
};
