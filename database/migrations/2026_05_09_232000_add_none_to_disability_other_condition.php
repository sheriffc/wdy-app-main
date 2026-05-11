<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now    = now();
        $nextId = DB::table('option_list')->max('id') + 1;

        DB::table('option_list')->insertOrIgnore([
            'id'            => $nextId,
            'list_name'     => 'disability_other_condition',
            'item_id'       => 'none',
            'item_name'     => 'None',
            'item_extra'    => null,
            'display_order' => 0,
            'active'        => 1,
            'updated_at'    => $now,
            'created_at'    => $now,
            'synced_at'     => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('option_list')
            ->where('list_name', 'disability_other_condition')
            ->where('item_id', 'none')
            ->delete();
    }
};
