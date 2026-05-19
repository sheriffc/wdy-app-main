<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $sssParentId = DB::table('option_list')
            ->where('list_name', 'rudimentary_education_level')
            ->where('item_id', 'sss')
            ->value('id');

        if (!$sssParentId) return;

        // Deactivate subjects no longer in the list
        DB::table('option_list')
            ->where('list_name', 'school_subject')
            ->where('parent_id', $sssParentId)
            ->whereIn('item_id', ['sss_government', 'sss_ict'])
            ->update(['active' => 0, 'updated_at' => $now]);

        // Rename + reorder existing subjects
        $updates = [
            'sss_english'       => ['item_name' => 'English Language',            'display_order' => 1],
            'sss_maths'         => ['item_name' => 'Mathematics',                  'display_order' => 2],
            'sss_further_maths' => ['item_name' => 'Further Mathematics',          'display_order' => 3],
            'sss_biology'       => ['item_name' => 'Biology',                      'display_order' => 4],
            'sss_chemistry'     => ['item_name' => 'Chemistry',                    'display_order' => 5],
            'sss_physics'       => ['item_name' => 'Physics',                      'display_order' => 6],
            'sss_literature'    => ['item_name' => 'English Literature',           'display_order' => 10],
            'sss_history'       => ['item_name' => 'History',                      'display_order' => 11],
            'sss_geography'     => ['item_name' => 'Geography',                    'display_order' => 12],
            'sss_rel_know'      => ['item_name' => 'Religious & Moral Education',  'display_order' => 14],
            'sss_french'        => ['item_name' => 'French',                       'display_order' => 16],
            'sss_economics'     => ['item_name' => 'Economics',                    'display_order' => 21],
        ];

        foreach ($updates as $itemId => $values) {
            DB::table('option_list')
                ->where('list_name', 'school_subject')
                ->where('parent_id', $sssParentId)
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
            ['item_id' => 'sss_health_sci',  'item_name' => 'Health Science',                              'display_order' => 7],
            ['item_id' => 'sss_agric',        'item_name' => 'Agricultural Science',                        'display_order' => 8],
            ['item_id' => 'sss_env_sci',      'item_name' => 'Environmental Science',                       'display_order' => 9],
            ['item_id' => 'sss_civic',        'item_name' => 'Civic Education',                             'display_order' => 13],
            ['item_id' => 'sss_creative',     'item_name' => 'Creative Practical Arts',                     'display_order' => 15],
            ['item_id' => 'sss_arabic',       'item_name' => 'Arabic',                                      'display_order' => 17],
            ['item_id' => 'sss_local_lang',   'item_name' => 'Local Languages (Krio, Mende, Themne, Fula)', 'display_order' => 18],
            ['item_id' => 'sss_accounting',   'item_name' => 'Principles of Accounting',                    'display_order' => 19],
            ['item_id' => 'sss_business',     'item_name' => 'Business Studies',                            'display_order' => 20],
            ['item_id' => 'sss_commerce',     'item_name' => 'Commerce',                                    'display_order' => 22],
            ['item_id' => 'sss_entrep',       'item_name' => 'Entrepreneurship',                            'display_order' => 23],
            ['item_id' => 'sss_bus_mgmt',     'item_name' => 'Business Management',                         'display_order' => 24],
            ['item_id' => 'sss_insurance',    'item_name' => 'Insurance Management',                        'display_order' => 25],
            ['item_id' => 'sss_crm',          'item_name' => 'Customer Relationship Management',            'display_order' => 26],
            ['item_id' => 'sss_eng_sci',      'item_name' => 'Engineering Science',                         'display_order' => 27],
            ['item_id' => 'sss_computer',     'item_name' => 'Computer Science',                            'display_order' => 28],
            ['item_id' => 'sss_tech',         'item_name' => 'Technology',                                  'display_order' => 29],
            ['item_id' => 'sss_clerical',     'item_name' => 'Clerical Office Studies',                     'display_order' => 30],
        ];

        foreach ($newSubjects as $subject) {
            $inserted = DB::table('option_list')->insertOrIgnore([
                'id'            => $nextId,
                'parent_id'     => $sssParentId,
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

        $sssParentId = DB::table('option_list')
            ->where('list_name', 'rudimentary_education_level')
            ->where('item_id', 'sss')
            ->value('id');

        if (!$sssParentId) return;

        // Remove newly inserted subjects
        DB::table('option_list')
            ->where('list_name', 'school_subject')
            ->where('parent_id', $sssParentId)
            ->whereIn('item_id', [
                'sss_health_sci', 'sss_agric', 'sss_env_sci', 'sss_civic', 'sss_creative',
                'sss_arabic', 'sss_local_lang', 'sss_accounting', 'sss_business',
                'sss_commerce', 'sss_entrep', 'sss_bus_mgmt', 'sss_insurance',
                'sss_crm', 'sss_eng_sci', 'sss_computer', 'sss_tech', 'sss_clerical',
            ])
            ->delete();

        // Restore original names and display orders
        $originals = [
            'sss_english'       => ['item_name' => 'English Language',        'display_order' => 1],
            'sss_maths'         => ['item_name' => 'Mathematics',              'display_order' => 2],
            'sss_further_maths' => ['item_name' => 'Further Mathematics',      'display_order' => 3],
            'sss_physics'       => ['item_name' => 'Physics',                  'display_order' => 4],
            'sss_chemistry'     => ['item_name' => 'Chemistry',                'display_order' => 5],
            'sss_biology'       => ['item_name' => 'Biology',                  'display_order' => 6],
            'sss_economics'     => ['item_name' => 'Economics',                'display_order' => 7],
            'sss_geography'     => ['item_name' => 'Geography',                'display_order' => 8],
            'sss_history'       => ['item_name' => 'History',                  'display_order' => 9],
            'sss_literature'    => ['item_name' => 'Literature in English',    'display_order' => 11],
            'sss_french'        => ['item_name' => 'French',                   'display_order' => 12],
            'sss_rel_know'      => ['item_name' => 'Religious Knowledge',      'display_order' => 14],
        ];

        foreach ($originals as $itemId => $values) {
            DB::table('option_list')
                ->where('list_name', 'school_subject')
                ->where('parent_id', $sssParentId)
                ->where('item_id', $itemId)
                ->update([
                    'item_name'     => $values['item_name'],
                    'display_order' => $values['display_order'],
                    'updated_at'    => $now,
                ]);
        }

        // Re-activate deactivated subjects
        DB::table('option_list')
            ->where('list_name', 'school_subject')
            ->where('parent_id', $sssParentId)
            ->whereIn('item_id', ['sss_government', 'sss_ict'])
            ->update(['active' => 1, 'updated_at' => $now]);
    }
};
