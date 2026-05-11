<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class ProfileCompletion{
    
    public function teachersProfileCompletionBarChart($districtId){
        $districtWhereClause="";
        if($districtId != null){
            $districtWhereClause=" AND s.district_id = {$districtId} ";
        }
        $sql ="
            SELECT 
                COALESCE(round(SUM(required_fields_complete) / SUM(count_teacher) * 100, 2), 0) AS all_required_fields_complete,
                COALESCE(round(SUM(teacher_role) / SUM(count_teacher) * 100, 2), 0) AS teacher_role,
                COALESCE(round(SUM(start_date) / SUM(count_teacher) * 100, 2), 0) AS start_date,
                COALESCE(round(SUM(date_of_birth) / SUM(count_teacher) * 100, 2), 0) AS date_of_birth,
                COALESCE(round(SUM(address) / SUM(count_teacher) * 100, 2), 0) AS address,
                COALESCE(round(SUM(photo_registration) / SUM(count_teacher) * 100, 2), 0) AS photo_registration,
                COALESCE(round(SUM(fingerprint_registration) / SUM(count_teacher) * 100, 2), 0) AS fingerprint_registration,
                COALESCE(round(SUM(phone_number) / SUM(count_teacher) * 100, 2), 0) AS phone_number,
                COALESCE(round(SUM(email) / SUM(count_teacher) * 100, 2), 0) AS email,
                COALESCE(round(SUM(nin) / SUM(count_teacher) * 100, 2), 0) AS nin,
                SUM(count_teacher) AS total_teachers
            FROM (
                SELECT 
                    COUNT(t.teacher_role_oid) AS teacher_role,
                    COUNT(t.start_date) AS start_date,
                    COUNT(p.date_of_birth) AS date_of_birth,
                    COUNT(p.portrait_uuid) AS photo_registration,
                    SUM(CASE 
                        WHEN p.fp_li_uuid IS NULL AND p.fp_lt_uuid IS NULL 
                        AND p.fp_ri_uuid IS NULL AND p.fp_rt_uuid IS NULL 
                        THEN 0 ELSE 1 END) AS fingerprint_registration,
                    SUM(CASE WHEN p.phone_1 IS NULL AND p.phone_2 IS NULL THEN 0 ELSE 1 END) AS phone_number,
                    COUNT(p.email) AS email,
                    COUNT(p.address) AS address,
                    COUNT(p.nin) AS nin,
                    COUNT(t.person_uuid) AS count_teacher,
                    SUM(CASE 
                        WHEN t.teacher_role_oid IS NULL OR t.start_date IS NULL 
                        OR p.date_of_birth IS NULL OR p.sex_oid IS NULL 
                        OR p.portrait_uuid IS NULL OR p.address IS NULL 
                        OR p.first_name IS NULL OR p.last_name IS NULL 
                        THEN 0 ELSE 1 END) AS required_fields_complete
                FROM school s
                LEFT JOIN teacher t ON s.uuid = t.school_uuid AND t.deleted_by IS NULL
                LEFT JOIN person p ON t.person_uuid = p.uuid
                LEFT JOIN district_office do ON s.district_id = do.district_id
                WHERE s.active = 1 
                    {$districtWhereClause}
                GROUP BY s.uuid
            ) AS d    
        ";
        return DB::selectOne($sql);
    }

    public function schoolTeachersProfileCompletionTable($districtId,$isDistrictOfficerOrAbove){
        $confidentialColumns = "
            null school_leader_name,
            null school_leader_phone_number,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns=" 
                s_leader.school_leader_name,
                s_leader.phone_number school_leader_phone_number,
            ";
        }

        $districtWhereClause="";
        if($districtId != null){
            $districtWhereClause=" AND s.district_id = {$districtId} ";
        }
        $sql="
            SELECT
                s.uuid school_uuid,
                s.name school_name, 
                {$confidentialColumns}
                do.name district_name,
                COUNT(t.teacher_role_oid) AS teacher_role,
                COUNT(t.start_date) AS start_date,
                COUNT(p.date_of_birth) AS date_of_birth,
                COUNT(p.portrait_uuid) AS photo_registration,
                SUM(CASE 
                    WHEN p.fp_li_uuid IS NULL AND p.fp_lt_uuid IS NULL 
                    AND p.fp_ri_uuid IS NULL AND p.fp_rt_uuid IS NULL 
                    THEN 0 ELSE 1 END) AS fingerprint_registration,
                SUM(CASE WHEN p.phone_1 IS NULL AND p.phone_2 IS NULL THEN 0 ELSE 1 END) AS phone_number,
                COUNT(p.email) AS email,
                COUNT(p.address) AS address,
                COUNT(p.nin) AS nin,
                COUNT(t.person_uuid) AS count_teacher,
                SUM(CASE 
                    WHEN t.teacher_role_oid IS NULL OR t.start_date IS NULL 
                    OR p.date_of_birth IS NULL OR p.sex_oid IS NULL 
                    OR p.portrait_uuid IS NULL OR p.address IS NULL 
                    OR p.first_name IS NULL OR p.last_name IS NULL 
                    THEN 0 ELSE 1 END) AS required_fields_complete
            FROM school s
            LEFT JOIN teacher t ON s.uuid = t.school_uuid AND t.deleted_by IS NULL
            LEFT JOIN person p ON t.person_uuid = p.uuid
            LEFT JOIN district_office do ON s.district_id = do.district_id
            LEFT JOIN (
                SELECT
                    t.school_uuid,
                    CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) school_leader_name,
                    IFNULL(p.phone_1,p.phone_2) phone_number
                FROM teacher t
                LEFT JOIN person p ON p.uuid = t.person_uuid
                WHERE (teacher_role_oid = 'head_teacher' OR teacher_role_oid = 'vice_principal')
                GROUP BY school_uuid
            ) s_leader ON s_leader.school_uuid = s.uuid
            WHERE s.active = 1 
                {$districtWhereClause}
            GROUP BY s.uuid

        ";
        return DB::select($sql);
    }

    public function learnersProfileCompletionBarChart($districtId){
        $districtWhereClause="";
        if($districtId != null){
            $districtWhereClause=" AND s.district_id = {$districtId} ";
        }
        $sql ="
            SELECT  
                COALESCE(round(SUM( required_fields_complete)/SUM( count_learner) * 100, 2), 0) AS all_required_fields_complete,
                COALESCE(round(SUM( date_of_birth)/SUM( count_learner) * 100, 2), 0) AS date_of_birth,
                COALESCE(round(SUM( sex)/SUM( count_learner) * 100, 2),0) AS sex,
                COALESCE(round(SUM( nin)/SUM( count_learner) * 100, 2),0) AS nin,
                COALESCE(round(SUM( admission_number)/SUM( count_learner) * 100, 2),0) AS admission_number,
                COALESCE(round(SUM( strongest_language)/SUM( count_learner) * 100, 2),0) AS strongest_language,
                COALESCE(round(SUM( maternal_status)/SUM( count_learner) * 100, 2),0)	AS maternal_status,
                COALESCE(round(SUM( complete_needs_assessment)/SUM( count_learner) * 100, 2),0) AS complete_needs_assessment,
                COALESCE(round(SUM( guardian_name)/SUM( count_learner) * 100, 2),0) AS guardian_name,
                COALESCE(round(SUM( guardian_phone)/SUM( count_learner) * 100, 2),0) AS guardian_phone,
                COALESCE(round(SUM( guardian_address)/SUM( count_learner) * 100, 2),0) AS guardian_address ,
                SUM( count_learner) AS total_learners
            FROM (
                SELECT 
                    COUNT(p.date_of_birth) AS date_of_birth,
                    COUNT(p.sex_oid) AS sex,
                    COUNT(p.nin) AS nin, 
                    COUNT(sla.admission_number) AS admission_number,
                    COUNT(l.language_oid_strongest) AS strongest_language,
                    SUM(CASE
                        WHEN (l.maternal_status_oid IS NULL AND p.sex_oid = 'female') OR
                        (l.maternal_status_oid IS NULL AND p.sex_oid IS NULL) THEN 0
                        ELSE 1 END) AS maternal_status,
                    SUM(CASE
                        WHEN l.disability_severity_oid_vision IS NULL
                            OR l.disability_severity_oid_hearing IS NULL
                            OR l.disability_severity_oid_mobility IS NULL
                            OR l.disability_severity_oid_cognition IS NULL
                            OR l.disability_severity_oid_selfcare IS NULL
                            OR l.disability_severity_oid_communication IS NULL
                            OR disability_other_condition_oid IS NULL 
                        THEN 0
                        ELSE 1 END) AS complete_needs_assessment,
                    COUNT(gp.first_name ) AS guardian_name,
                    SUM(CASE WHEN gp.phone_1 IS NULL AND gp.phone_2 IS NULL THEN 0 ELSE 1 END) AS guardian_phone,
                    COUNT(gp.address) AS guardian_address,
                    COUNT(l.person_uuid) as count_learner,
                    SUM(CASE
                        WHEN l.disability_severity_oid_vision IS NULL
                            OR l.disability_severity_oid_hearing IS NULL
                            OR l.disability_severity_oid_mobility IS NULL
                            OR l.disability_severity_oid_cognition IS NULL
                            OR l.disability_severity_oid_selfcare IS NULL
                            OR l.disability_severity_oid_communication IS NULL
                            OR disability_other_condition_oid IS NULL
                            OR p.date_of_birth IS NULL
                            OR gp.first_name IS NULL
                            OR gp.address IS NULL
                            OR p.sex_oid IS NULL
                            OR p.first_name IS NULL
                            OR p.last_name IS NULL
                            OR l.language_oid_strongest IS NULL
                            OR (l.maternal_status_oid IS NULL AND p.sex_oid = 'female')
                            OR (l.maternal_status_oid IS NULL AND p.sex_oid IS NULL)
                        THEN 0
                        ELSE 1 END) AS required_fields_complete,
                    s.name as school_name, do.name as district_name
                FROM school s
                LEFT JOIN school_learner_admission sla ON s.uuid = sla.school_uuid
                LEFT JOIN learner l ON sla.learner_uuid = l.uuid
                LEFT JOIN person p ON l.person_uuid = p.uuid
                LEFT JOIN person gp ON l.guardian_person_uuid = gp.uuid
                LEFT JOIN district_office do ON s.district_id = do.district_id
                WHERE s.active = 1 
                    {$districtWhereClause}
                    AND l.deleted_by IS NULL
            ) AS d
        ";
        return DB::selectOne($sql);
    }

    public function schoolLearnersProfileCompletionTable($districtId, $isDistrictOfficerOrAbove){

        $confidentialColumns = "
            null school_leader_name,
            null school_leader_phone_number,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns=" 
                s_leader.school_leader_name,
                s_leader.phone_number school_leader_phone_number,
            ";
        }

        $districtWhereClause="";
        if($districtId != null){
            $districtWhereClause=" AND s.district_id = {$districtId} ";
        }
        $sql="
            SELECT 
                    s.uuid school_uuid,
                    s.name school_name, 
					{$confidentialColumns}
                    do.name district_name,
                    COUNT(p.date_of_birth) AS date_of_birth,
                    COUNT(p.sex_oid) AS sex,
                    COUNT(p.nin) AS nin, 
                    COUNT(sla.admission_number) AS admission_number,
                    COUNT(l.language_oid_strongest) AS strongest_language,
                    SUM(CASE
                        WHEN (l.maternal_status_oid IS NULL AND p.sex_oid = 'female') OR
                        (l.maternal_status_oid IS NULL AND p.sex_oid IS NULL) THEN 0
                        ELSE 1 END) AS maternal_status,
                    SUM(CASE
                        WHEN l.disability_severity_oid_vision IS NULL
                            OR l.disability_severity_oid_hearing IS NULL
                            OR l.disability_severity_oid_mobility IS NULL
                            OR l.disability_severity_oid_cognition IS NULL
                            OR l.disability_severity_oid_selfcare IS NULL
                            OR l.disability_severity_oid_communication IS NULL
                            OR disability_other_condition_oid IS NULL 
                        THEN 0
                        ELSE 1 END) AS complete_needs_assessment,
                    COUNT(gp.first_name ) AS guardian_name,
                    SUM(CASE WHEN gp.phone_1 IS NULL AND gp.phone_2 IS NULL THEN 0 ELSE 1 END) AS guardian_phone,
                    COUNT(gp.address) AS guardian_address,
                    COUNT(l.person_uuid) as count_learner,
                    SUM(CASE
                        WHEN l.disability_severity_oid_vision IS NULL
                            OR l.disability_severity_oid_hearing IS NULL
                            OR l.disability_severity_oid_mobility IS NULL
                            OR l.disability_severity_oid_cognition IS NULL
                            OR l.disability_severity_oid_selfcare IS NULL
                            OR l.disability_severity_oid_communication IS NULL
                            OR disability_other_condition_oid IS NULL
                            OR p.date_of_birth IS NULL
                            OR gp.first_name IS NULL
                            OR gp.address IS NULL
                            OR p.sex_oid IS NULL
                            OR p.first_name IS NULL
                            OR p.last_name IS NULL
                            OR l.language_oid_strongest IS NULL
                            OR (l.maternal_status_oid IS NULL AND p.sex_oid = 'female')
                            OR (l.maternal_status_oid IS NULL AND p.sex_oid IS NULL)
                        THEN 0
                        ELSE 1 END) AS required_fields_complete
                FROM school s
                LEFT JOIN school_learner_admission sla ON s.uuid = sla.school_uuid
                LEFT JOIN learner l ON sla.learner_uuid = l.uuid
                LEFT JOIN person p ON l.person_uuid = p.uuid
                LEFT JOIN person gp ON l.guardian_person_uuid = gp.uuid
                LEFT JOIN district_office do ON s.district_id = do.district_id
                LEFT JOIN (
					SELECT
						t.school_uuid,
						CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) school_leader_name,
						IFNULL(p.phone_1,p.phone_2) phone_number
					FROM teacher t
					LEFT JOIN person p ON p.uuid = t.person_uuid
					WHERE (teacher_role_oid = 'head_teacher' OR teacher_role_oid = 'vice_principal')
					GROUP BY school_uuid
				) s_leader ON s_leader.school_uuid = s.uuid
                WHERE s.active = 1 
                    {$districtWhereClause}
                    AND l.deleted_by IS NULL
                GROUP BY s.uuid
        ";
        return DB::select($sql);
    }

    public function getSchoolsProfileCompletionTable($districtId,$isDistrictOfficerOrAbove){
        $confidentialColumns = "
            null school_leader_name,
            null school_leader_phone_number,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns=" 
                school_leader_name,
                school_leader_phone_number,
            ";
        }

        $districtWhereClause="";
        if($districtId != null){
            $districtWhereClause=" WHERE district_id = {$districtId} ";
        }

        $sql="
            SELECT 
                uuid school_uuid,
                name school_name,
                {$confidentialColumns}
                teacher_total,
                teacher_profile_required_fields_complete,
                teacher_profile_role,
                teacher_profile_photo,
                teacher_profile_fingerprint_registration,
                teacher_profile_phone_number,
                teacher_profile_address,
                teacher_profile_email,
                teacher_profile_start_date,
                teacher_profile_nin,
                learners_total,
                learner_profile_required_fields_complete,
                learner_profile_date_of_birth,
                learner_profile_sex,
                learner_profile_nin,
                learner_profile_admission_number,
                learner_profile_strongest_language,
                learner_profile_maternal_status,
                learner_profile_complete_needs_assessment,
                learner_profile_guardian_name,
                learner_profile_guardian_phone,
                learner_profile_guardian_address
            FROM cache_school_info
            {$districtWhereClause}
        ";
        return DB::select($sql);
    }

    public function schoolProfileCompletionCharts($districtId,$isDistrictOfficerOrAbove){
        $profileCompletionTable = collect($this->getSchoolsProfileCompletionTable($districtId,$isDistrictOfficerOrAbove));

        $sumTeachers = $profileCompletionTable->sum("teacher_total");
        $sumLearners = $profileCompletionTable->sum("learners_total");

        $teacherProfileCompletionBarChart=[
            "required_fields_complete" => ($sumTeachers > 0 ? round($profileCompletionTable->sum("teacher_profile_required_fields_complete") / $sumTeachers*100,0) : 0) ,
            "teacher_role" => ($sumTeachers > 0 ? round($profileCompletionTable->sum("teacher_profile_role") / $sumTeachers*100,0): 0),
            "photo_registration" => ($sumTeachers > 0 ? round($profileCompletionTable->sum("teacher_profile_photo") / $sumTeachers*100,0): 0),
            "fingerprint_registration" => ($sumTeachers > 0 ? round($profileCompletionTable->sum("teacher_profile_fingerprint_registration") / $sumTeachers*100,0): 0),
            "phone_number" => ($sumTeachers > 0 ? round($profileCompletionTable->sum("teacher_profile_phone_number") / $sumTeachers*100,0): 0),
            "address" => ($sumTeachers > 0 ? round($profileCompletionTable->sum("teacher_profile_address") / $sumTeachers*100,0): 0),
            "email" => ($sumTeachers > 0 ? round($profileCompletionTable->sum("teacher_profile_email") / $sumTeachers*100,0): 0),
            "start_date" => ($sumTeachers > 0 ? round($profileCompletionTable->sum("teacher_profile_start_date") / $sumTeachers*100,0): 0),
            "nin" => ($sumTeachers > 0 ? round($profileCompletionTable->sum("teacher_profile_nin") / $sumTeachers*100,0): 0)
        ];

        $learnerProfileCompletionBarChart=[
            "required_fields_complete" => ($sumLearners > 0 ? round($profileCompletionTable->sum("learner_profile_required_fields_complete") / $sumLearners*100,0) : 0),
            "complete_needs_assessment" => ($sumLearners > 0 ? round($profileCompletionTable->sum("learner_profile_complete_needs_assessment") / $sumLearners*100,0) : 0),
            "maternal_status" => ($sumLearners > 0 ? round($profileCompletionTable->sum("learner_profile_maternal_status") / $sumLearners*100,0) : 0),
            "guardian_name" => ($sumLearners > 0 ? round($profileCompletionTable->sum("learner_profile_guardian_name") / $sumLearners*100,0) : 0),
            "guardian_address" => ($sumLearners > 0 ? round($profileCompletionTable->sum("learner_profile_guardian_address") / $sumLearners*100,0) : 0),
            "guardian_phone" => ($sumLearners > 0 ? round($profileCompletionTable->sum("learner_profile_guardian_phone") / $sumLearners*100,0) : 0),
            "admission_number" => ($sumLearners > 0 ? round($profileCompletionTable->sum("learner_profile_admission_number") / $sumLearners*100,0) : 0),
            "nin" => ($sumLearners > 0 ? round($profileCompletionTable->sum("learner_profile_nin") / $sumLearners*100,0) : 0),
            "date_of_birth" => ($sumLearners > 0 ? round($profileCompletionTable->sum("learner_profile_date_of_birth") / $sumLearners*100,0) : 0),
            "sex" => ($sumLearners > 0 ? round($profileCompletionTable->sum("learner_profile_sex") / $sumLearners*100,0) : 0),
            "strongest_language" => ($sumLearners > 0 ? round($profileCompletionTable->sum("learner_profile_strongest_language") / $sumLearners*100,0) : 0),
        ];

        $schoolTeachersProfileCompletionTable = array();
        $profileCompletionTable->each(function ($item) use(&$schoolTeachersProfileCompletionTable){
                array_push($schoolTeachersProfileCompletionTable,[
                    "school_uuid" => $item->school_uuid,
                    "school_name" => $item->school_name,
                    "school_leader_name" => $item->school_leader_name,
                    "school_leader_phone_number" => $item->school_leader_phone_number,
                    "required_fields_complete" =>($item->teacher_total > 0) ? round($item->teacher_profile_required_fields_complete / $item->teacher_total *100,0) : 0,
                    "teacher_role" => ($item->teacher_total > 0) ? round($item->teacher_profile_role / $item->teacher_total*100,0) : 0,
                    "photo_registration" => ($item->teacher_total > 0) ? round($item->teacher_profile_photo / $item->teacher_total*100,0) : 0,
                    "fingerprint_registration" => ($item->teacher_total > 0) ? round($item->teacher_profile_fingerprint_registration / $item->teacher_total*100,0) : 0,
                    "phone_number" => ($item->teacher_total > 0) ? round($item->teacher_profile_phone_number / $item->teacher_total*100,0) : 0,
                    "address" => ($item->teacher_total > 0) ? round($item->teacher_profile_address / $item->teacher_total*100,0) : 0,
                    "email" => ($item->teacher_total > 0) ? round($item->teacher_profile_email / $item->teacher_total*100,0) : 0,
                    "start_date" => ($item->teacher_total > 0) ? round($item->teacher_profile_start_date / $item->teacher_total*100,0) : 0,
                    "nin" => ($item->teacher_total > 0) ? round($item->teacher_profile_nin / $item->teacher_total*100,0) : 0
                ]);
            });

        $schoolLearnersProfileCompletionTable = array();
        $profileCompletionTable->each(function ($item) use(&$schoolLearnersProfileCompletionTable){
                array_push($schoolLearnersProfileCompletionTable,[
                    "school_uuid" => $item->school_uuid,
                    "school_name" => $item->school_name,
                    "school_leader_name" => $item->school_leader_name,
                    "school_leader_phone_number" => $item->school_leader_phone_number,
                    "required_fields_complete" =>($item->learners_total > 0) ? round($item->learner_profile_required_fields_complete / $item->learners_total *100,0) : 0,
                    "date_of_birth" => ($item->learners_total > 0) ? round($item->learner_profile_date_of_birth / $item->learners_total*100,0) : 0,
                    "sex" => ($item->learners_total > 0) ? round($item->learner_profile_sex / $item->learners_total*100,0) : 0,
                    "admission_number" => ($item->learners_total > 0) ? round($item->learner_profile_admission_number / $item->learners_total*100,0) : 0,
                    "strongest_language" => ($item->learners_total > 0) ? round($item->learner_profile_strongest_language / $item->learners_total*100,0) : 0,
                    "maternal_status" => ($item->learners_total > 0) ? round($item->learner_profile_maternal_status / $item->learners_total*100,0) : 0,
                    "complete_needs_assessment" => ($item->learners_total > 0) ? round($item->learner_profile_complete_needs_assessment / $item->learners_total*100,0) : 0,
                    "guardian_name" => ($item->learners_total > 0) ? round($item->learner_profile_guardian_name / $item->learners_total*100,0) : 0,
                    "guardian_phone" => ($item->learners_total > 0) ? round($item->learner_profile_guardian_phone / $item->learners_total*100,0) : 0,
                    "guardian_address" => ($item->learners_total > 0) ? round($item->learner_profile_guardian_address / $item->learners_total*100,0) : 0,
                    "nin" => ($item->learners_total > 0) ? round($item->learner_profile_nin / $item->learners_total*100,0) : 0,
                ]);
            });

        return [
            "schoolTeachersProfileCompletionTable" =>  $schoolTeachersProfileCompletionTable,
            "teacherProfileCompletionBarChart" =>  $teacherProfileCompletionBarChart,
            "learnerProfileCompletionBarChart" =>  $learnerProfileCompletionBarChart,
            "schoolLearnersProfileCompletionTable" =>  $schoolLearnersProfileCompletionTable,
        ];
    }


}