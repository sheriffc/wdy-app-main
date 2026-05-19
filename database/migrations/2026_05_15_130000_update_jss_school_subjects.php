<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $jssParentId = DB::table('option_list')
            ->where('list_name', 'rudimentary_education_level')
            ->where('item_id', 'jss')
            ->value('id');

        if (!$jssParentId) return;

        // Rename existing subjects
        $renames = [
            'jss_english'  => 'Language Arts',
            'jss_creative' => 'Creative Practical Arts',
            'jss_ict'      => 'Technology',
        ];

        foreach ($renames as $itemId => $newName) {
            DB::table('option_list')
                ->where('list_name', 'school_subject')
                ->where('parent_id', $jssParentId)
                ->where('item_id', $itemId)
                ->update(['item_name' => $newName, 'updated_at' => $now]);
        }

        // Insert new subjects
        $nextId = DB::table('option_list')->max('id') + 1;

        $newSubjects = [
            ['item_id' => 'jss_sl_languages', 'item_name' => 'SL Languages',    'display_order' => 12],
            ['item_id' => 'jss_business',      'item_name' => 'Business Studies', 'display_order' => 13],
        ];

        foreach ($newSubjects as $subject) {
            $inserted = DB::table('option_list')->insertOrIgnore([
                'id'            => $nextId,
                'parent_id'     => $jssParentId,
                'list_name'     => 'school_subject',
                'item_id'       => $subject['item_id'],
                'item_name'     => $subject['item_name'],
                'item_extra'    => null,
                'display_order' => $subject['display_order'],
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
        $now = now();

        $jssParentId = DB::table('option_list')
            ->where('list_name', 'rudimentary_education_level')
            ->where('item_id', 'jss')
            ->value('id');

        if (!$jssParentId) return;

        // Restore original names
        $originals = [
            'jss_english'  => 'English Language',
            'jss_creative' => 'Creative Arts',
            'jss_ict'      => 'ICT / Computer Studies',
        ];

        foreach ($originals as $itemId => $originalName) {
            DB::table('option_list')
                ->where('list_name', 'school_subject')
                ->where('parent_id', $jssParentId)
                ->where('item_id', $itemId)
                ->update(['item_name' => $originalName, 'updated_at' => $now]);
        }

        // Remove newly inserted subjects
        DB::table('option_list')
            ->where('list_name', 'school_subject')
            ->where('parent_id', $jssParentId)
            ->whereIn('item_id', ['jss_sl_languages', 'jss_business'])
            ->delete();
    }
};
