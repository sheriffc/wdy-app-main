<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now    = now();
        $nextId = DB::table('option_list')->max('id') + 1;

        $statuses = [
            ['item_id' => 'none',      'item_name' => 'None',             'display_order' => 1],
            ['item_id' => 'preg',      'item_name' => 'Pregnant',         'display_order' => 2],
            ['item_id' => 'moth',      'item_name' => 'Mother',           'display_order' => 3],
            ['item_id' => 'preg_moth', 'item_name' => 'Pregnant Mother',  'display_order' => 4],
        ];

        foreach ($statuses as $status) {
            $inserted = DB::table('option_list')->insertOrIgnore([
                'id'            => $nextId,
                'list_name'     => 'maternal_status',
                'item_id'       => $status['item_id'],
                'item_name'     => $status['item_name'],
                'item_extra'    => null,
                'display_order' => $status['display_order'],
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
            ->where('list_name', 'maternal_status')
            ->delete();
    }
};
