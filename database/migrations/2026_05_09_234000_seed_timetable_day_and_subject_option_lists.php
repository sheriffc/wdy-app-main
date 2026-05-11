<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now    = now();
        $nextId = DB::table('option_list')->max('id') + 1;

        // --- Days of the week ---
        $days = [
            ['item_id' => 'monday',    'item_name' => 'Monday',    'display_order' => 1],
            ['item_id' => 'tuesday',   'item_name' => 'Tuesday',   'display_order' => 2],
            ['item_id' => 'wednesday', 'item_name' => 'Wednesday', 'display_order' => 3],
            ['item_id' => 'thursday',  'item_name' => 'Thursday',  'display_order' => 4],
            ['item_id' => 'friday',    'item_name' => 'Friday',    'display_order' => 5],
        ];

        foreach ($days as $day) {
            $inserted = DB::table('option_list')->insertOrIgnore([
                'id'            => $nextId,
                'list_name'     => 'day_of_the_week',
                'item_id'       => $day['item_id'],
                'item_name'     => $day['item_name'],
                'item_extra'    => null,
                'display_order' => $day['display_order'],
                'active'        => 1,
                'updated_at'    => $now,
                'created_at'    => $now,
                'synced_at'     => $now,
            ]);
            if ($inserted) $nextId++;
        }

        // --- Rudimentary education levels (middle tier linking education level → subject) ---
        // school_education_level IDs: pre=57, pri=58, jss=59, sss=60
        $rudimentaryLevels = [
            ['item_id' => 'pre_primary', 'item_name' => 'Pre-Primary', 'display_order' => 1, 'school_education_level_id' => 57],
            ['item_id' => 'primary',     'item_name' => 'Primary',     'display_order' => 2, 'school_education_level_id' => 58],
            ['item_id' => 'jss',         'item_name' => 'JSS',         'display_order' => 3, 'school_education_level_id' => 59],
            ['item_id' => 'sss',         'item_name' => 'SSS',         'display_order' => 4, 'school_education_level_id' => 60],
        ];

        $rudimentaryIds = []; // keyed by item_id

        foreach ($rudimentaryLevels as $level) {
            $inserted = DB::table('option_list')->insertOrIgnore([
                'id'            => $nextId,
                'list_name'     => 'rudimentary_education_level',
                'item_id'       => $level['item_id'],
                'item_name'     => $level['item_name'],
                'item_extra'    => null,
                'display_order' => $level['display_order'],
                'active'        => 1,
                'updated_at'    => $now,
                'created_at'    => $now,
                'synced_at'     => $now,
            ]);
            if ($inserted) {
                $rudimentaryIds[$level['item_id']] = $nextId;
                $nextId++;
            } else {
                // Already existed — look it up
                $row = DB::table('option_list')
                    ->where('list_name', 'rudimentary_education_level')
                    ->where('item_id', $level['item_id'])
                    ->first();
                $rudimentaryIds[$level['item_id']] = $row->id;
            }
        }

        // --- option_list_link: school_education_level → rudimentary_education_level ---
        $nextLinkId = DB::table('option_list_link')->max('id') + 1;

        foreach ($rudimentaryLevels as $level) {
            $exists = DB::table('option_list_link')
                ->where('parent_list_name', 'school_education_level')
                ->where('child_list_name', 'rudimentary_education_level')
                ->where('parent_id', $level['school_education_level_id'])
                ->where('child_id', $rudimentaryIds[$level['item_id']])
                ->exists();

            if (!$exists) {
                DB::table('option_list_link')->insert([
                    'id'               => $nextLinkId,
                    'parent_list_name' => 'school_education_level',
                    'child_list_name'  => 'rudimentary_education_level',
                    'parent_id'        => $level['school_education_level_id'],
                    'child_id'         => $rudimentaryIds[$level['item_id']],
                    'updated_at'       => $now,
                    'created_at'       => $now,
                    'synced_at'        => $now,
                ]);
                $nextLinkId++;
            }
        }

        // --- School subjects per rudimentary education level ---
        $subjects = [

            'pre_primary' => [
                ['item_id' => 'pre_english',   'item_name' => 'English Language', 'display_order' => 1],
                ['item_id' => 'pre_maths',      'item_name' => 'Mathematics',      'display_order' => 2],
                ['item_id' => 'pre_creative',   'item_name' => 'Creative Arts',    'display_order' => 3],
                ['item_id' => 'pre_pe',         'item_name' => 'Physical Education','display_order' => 4],
            ],

            'primary' => [
                ['item_id' => 'pri_english',    'item_name' => 'English Language',          'display_order' => 1],
                ['item_id' => 'pri_maths',      'item_name' => 'Mathematics',               'display_order' => 2],
                ['item_id' => 'pri_science',    'item_name' => 'Integrated Science',        'display_order' => 3],
                ['item_id' => 'pri_social',     'item_name' => 'Social Studies',            'display_order' => 4],
                ['item_id' => 'pri_creative',   'item_name' => 'Creative Arts',             'display_order' => 5],
                ['item_id' => 'pri_rme',        'item_name' => 'Religious & Moral Education','display_order' => 6],
                ['item_id' => 'pri_pe',         'item_name' => 'Physical Education',        'display_order' => 7],
            ],

            'jss' => [
                ['item_id' => 'jss_english',    'item_name' => 'English Language',          'display_order' => 1],
                ['item_id' => 'jss_maths',      'item_name' => 'Mathematics',               'display_order' => 2],
                ['item_id' => 'jss_science',    'item_name' => 'Integrated Science',        'display_order' => 3],
                ['item_id' => 'jss_social',     'item_name' => 'Social Studies',            'display_order' => 4],
                ['item_id' => 'jss_creative',   'item_name' => 'Creative Arts',             'display_order' => 5],
                ['item_id' => 'jss_rme',        'item_name' => 'Religious & Moral Education','display_order' => 6],
                ['item_id' => 'jss_pe',         'item_name' => 'Physical Education',        'display_order' => 7],
                ['item_id' => 'jss_french',     'item_name' => 'French',                    'display_order' => 8],
                ['item_id' => 'jss_home_ec',    'item_name' => 'Home Economics',            'display_order' => 9],
                ['item_id' => 'jss_agric',      'item_name' => 'Agricultural Science',      'display_order' => 10],
                ['item_id' => 'jss_ict',        'item_name' => 'ICT / Computer Studies',    'display_order' => 11],
            ],

            'sss' => [
                ['item_id' => 'sss_english',    'item_name' => 'English Language',          'display_order' => 1],
                ['item_id' => 'sss_maths',      'item_name' => 'Mathematics',               'display_order' => 2],
                ['item_id' => 'sss_further_maths','item_name' => 'Further Mathematics',     'display_order' => 3],
                ['item_id' => 'sss_physics',    'item_name' => 'Physics',                   'display_order' => 4],
                ['item_id' => 'sss_chemistry',  'item_name' => 'Chemistry',                 'display_order' => 5],
                ['item_id' => 'sss_biology',    'item_name' => 'Biology',                   'display_order' => 6],
                ['item_id' => 'sss_economics',  'item_name' => 'Economics',                 'display_order' => 7],
                ['item_id' => 'sss_geography',  'item_name' => 'Geography',                 'display_order' => 8],
                ['item_id' => 'sss_history',    'item_name' => 'History',                   'display_order' => 9],
                ['item_id' => 'sss_government', 'item_name' => 'Government',                'display_order' => 10],
                ['item_id' => 'sss_literature', 'item_name' => 'Literature in English',     'display_order' => 11],
                ['item_id' => 'sss_french',     'item_name' => 'French',                    'display_order' => 12],
                ['item_id' => 'sss_ict',        'item_name' => 'ICT / Computer Studies',    'display_order' => 13],
                ['item_id' => 'sss_rel_know',   'item_name' => 'Religious Knowledge',       'display_order' => 14],
            ],
        ];

        foreach ($subjects as $levelKey => $levelSubjects) {
            $parentId = $rudimentaryIds[$levelKey];
            foreach ($levelSubjects as $subject) {
                $inserted = DB::table('option_list')->insertOrIgnore([
                    'id'            => $nextId,
                    'parent_id'     => $parentId,
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
    }

    public function down(): void
    {
        DB::table('option_list')
            ->whereIn('list_name', ['day_of_the_week', 'rudimentary_education_level', 'school_subject'])
            ->delete();

        DB::table('option_list_link')
            ->where('parent_list_name', 'school_education_level')
            ->where('child_list_name', 'rudimentary_education_level')
            ->delete();
    }
};
