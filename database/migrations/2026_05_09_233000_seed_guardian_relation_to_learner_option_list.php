<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now    = now();
        $nextId = DB::table('option_list')->max('id') + 1;

        $relations = [
            ['item_id' => 'mother',         'item_name' => 'Mother',         'display_order' => 1],
            ['item_id' => 'father',         'item_name' => 'Father',         'display_order' => 2],
            ['item_id' => 'grandparent',    'item_name' => 'Grandparent',    'display_order' => 3],
            ['item_id' => 'aunt_uncle',     'item_name' => 'Aunt / Uncle',   'display_order' => 4],
            ['item_id' => 'sibling',        'item_name' => 'Sibling',        'display_order' => 5],
            ['item_id' => 'legal_guardian', 'item_name' => 'Legal Guardian', 'display_order' => 6],
            ['item_id' => 'other',          'item_name' => 'Other',          'display_order' => 7],
        ];

        foreach ($relations as $relation) {
            $inserted = DB::table('option_list')->insertOrIgnore([
                'id'            => $nextId,
                'list_name'     => 'guardian_relation_to_learner',
                'item_id'       => $relation['item_id'],
                'item_name'     => $relation['item_name'],
                'item_extra'    => null,
                'display_order' => $relation['display_order'],
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
            ->where('list_name', 'guardian_relation_to_learner')
            ->delete();
    }
};
