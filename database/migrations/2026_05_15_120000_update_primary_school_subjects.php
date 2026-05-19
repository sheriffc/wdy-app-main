<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $primaryParentId = DB::table('option_list')
            ->where('list_name', 'rudimentary_education_level')
            ->where('item_id', 'primary')
            ->value('id');

        if (!$primaryParentId) return;

        // Deactivate subjects replaced by new equivalents
        DB::table('option_list')
            ->where('list_name', 'school_subject')
            ->where('parent_id', $primaryParentId)
            ->whereIn('item_id', ['pri_science', 'pri_pe'])
            ->update(['active' => 0, 'updated_at' => $now]);

        // Update names and display orders for subjects being kept
        $updates = [
            'pri_english'  => ['item_name' => 'English Language',            'display_order' => 1],
            'pri_maths'    => ['item_name' => 'Maths',                        'display_order' => 6],
            'pri_social'   => ['item_name' => 'Social Studies',               'display_order' => 7],
            'pri_creative' => ['item_name' => 'Practical Creative Arts',      'display_order' => 11],
            'pri_rme'      => ['item_name' => 'Religious & Moral Education',  'display_order' => 12],
        ];

        foreach ($updates as $itemId => $values) {
            DB::table('option_list')
                ->where('list_name', 'school_subject')
                ->where('parent_id', $primaryParentId)
                ->where('item_id', $itemId)
                ->update([
                    'item_name'     => $values['item_name'],
                    'display_order' => $values['display_order'],
                    'active'        => 1,
                    'updated_at'    => $now,
                ]);
        }

        // Insert new subjects
        $nextId = DB::table('option_list')->max('id') + 1;

        $newSubjects = [
            ['item_id' => 'pri_reading',     'item_name' => 'Reading & Comprehension',      'display_order' => 2],
            ['item_id' => 'pri_spelling',    'item_name' => 'Spelling & Dictation',         'display_order' => 3],
            ['item_id' => 'pri_poetry',      'item_name' => 'Poetry/Lit & Drama',           'display_order' => 4],
            ['item_id' => 'pri_composition', 'item_name' => 'Composition & Letter Writing', 'display_order' => 5],
            ['item_id' => 'pri_gen_science', 'item_name' => 'General Science',              'display_order' => 8],
            ['item_id' => 'pri_phe',         'item_name' => 'Physical Health Education',    'display_order' => 9],
            ['item_id' => 'pri_agric',       'item_name' => 'Agricultural Science',         'display_order' => 10],
            ['item_id' => 'pri_pre_voc',     'item_name' => 'Pre. Voc. Studies',            'display_order' => 13],
            ['item_id' => 'pri_french',      'item_name' => 'French',                       'display_order' => 14],
            ['item_id' => 'pri_quant',       'item_name' => 'Quantitative Aptitude',        'display_order' => 15],
            ['item_id' => 'pri_verbal',      'item_name' => 'Verbal Aptitude',              'display_order' => 16],
        ];

        foreach ($newSubjects as $subject) {
            $inserted = DB::table('option_list')->insertOrIgnore([
                'id'            => $nextId,
                'parent_id'     => $primaryParentId,
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

        $primaryParentId = DB::table('option_list')
            ->where('list_name', 'rudimentary_education_level')
            ->where('item_id', 'primary')
            ->value('id');

        if (!$primaryParentId) return;

        // Remove newly inserted subjects
        DB::table('option_list')
            ->where('list_name', 'school_subject')
            ->where('parent_id', $primaryParentId)
            ->whereIn('item_id', [
                'pri_reading', 'pri_spelling', 'pri_poetry', 'pri_composition',
                'pri_gen_science', 'pri_phe', 'pri_agric',
                'pri_pre_voc', 'pri_french', 'pri_quant', 'pri_verbal',
            ])
            ->delete();

        // Restore original names and display orders
        $originals = [
            'pri_english'  => ['item_name' => 'English Language',            'display_order' => 1],
            'pri_maths'    => ['item_name' => 'Mathematics',                  'display_order' => 2],
            'pri_social'   => ['item_name' => 'Social Studies',               'display_order' => 4],
            'pri_creative' => ['item_name' => 'Creative Arts',                'display_order' => 5],
            'pri_rme'      => ['item_name' => 'Religious & Moral Education',  'display_order' => 6],
        ];

        foreach ($originals as $itemId => $values) {
            DB::table('option_list')
                ->where('list_name', 'school_subject')
                ->where('parent_id', $primaryParentId)
                ->where('item_id', $itemId)
                ->update([
                    'item_name'     => $values['item_name'],
                    'display_order' => $values['display_order'],
                    'updated_at'    => $now,
                ]);
        }

        // Re-activate the deactivated subjects
        DB::table('option_list')
            ->where('list_name', 'school_subject')
            ->where('parent_id', $primaryParentId)
            ->whereIn('item_id', ['pri_science', 'pri_pe'])
            ->update(['active' => 1, 'updated_at' => $now]);
    }
};
