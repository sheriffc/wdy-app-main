<?php
namespace App\Queries;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function GuzzleHttp\Promise\settle;

class PopulateGeneralCacheTables {

    public static function setCacheAttendanceBySchoolDateColumns(){
            return "
                CONCAT(s.uuid,'-',@date) uuid,
                @date date,
                null,
                s.uuid school_uuid,
                s.name school_name,
                IFNULL(s.lat,0) lat,
                IFNULL(s.lng,0) lng,
                s.district_id,
                s.chiefdom_id,
                s.tablet_phone_number,
                school_contact.phone_number school_leader_phone_number,
                IFNULL(s_teachers.count,0) teachers_total,
                IFNULL(s_learners.count,0) learners_total,
                IFNULL(classroom.count_classrooms,0)count_classrooms,
                IFNULL(t_present.count,0) + IFNULL(t_late.count,0) + IFNULL(t_absent.count,0) teachers_reported,
                IFNULL(t_present.count,0) teachers_present,
                IFNULL(t_late.count,0) teachers_late,
                IFNULL(t_absent.count,0) teachers_absent,
                IFNULL(l_reported.count,0) learners_reported,
                IFNULL(l_reported.count_male,0) learners_male,
                IFNULL(l_reported.count_female,0) learners_female,
                IFNULL(l_reported.count_present,0) learners_present,
                IFNULL(l_reported.count_absent,0) learners_absent,
                IFNULL(l_reported.count_male_present,0) learners_male_present,
                IFNULL(l_reported.count_female_present,0) learners_female_present,
                IFNULL(l_reported.count_male_absent,0) learners_male_absent,
                IFNULL(l_reported.count_female_absent,0) learners_female_absent,
                IFNULL(l_reported.count_am_present,0) learners_am_present,
                IFNULL(l_reported.count_am_absent,0) learners_am_absent,
                IFNULL(l_reported.count_pm_present,0) learners_pm_present,
                IFNULL(l_reported.count_pm_absent,0) learners_pm_absent,
                IFNULL(l_reported.count_mother,0) maternal_learners_mothers,
                IFNULL(l_reported.count_pregnant,0) maternal_learners_pregnant,
                IFNULL(l_reported.count_pregnant_mother,0) maternal_learners_pregnant_mother,
                IFNULL(l_reported.count_status_none,0) maternal_learners_status_none,
                IFNULL(l_reported.count_am_present_mother,0) maternal_am_present_mother,
                IFNULL(l_reported.count_am_present_pregnant,0) maternal_am_present_pregnant,
                IFNULL(l_reported.count_am_present_pregnant_mother,0) maternal_am_present_pregnant_mother,
                IFNULL(l_reported.count_am_absent_mother,0) maternal_am_absent_mother,
                IFNULL(l_reported.count_am_absent_pregnant,0) maternal_am_absent_pregnant,
                IFNULL(l_reported.count_am_absent_pregnant_mother,0) maternal_am_absent_pregnant_mother,
                IFNULL(l_reported.count_pm_present_mother,0) maternal_pm_present_mother,
                IFNULL(l_reported.count_pm_present_pregnant,0) maternal_pm_present_pregnant,
                IFNULL(l_reported.count_pm_present_pregnant_mother,0) maternal_pm_present_pregnant_mother,
                IFNULL(l_reported.count_pm_absent_mother,0) maternal_pm_absent_mother,
                IFNULL(l_reported.count_pm_absent_pregnant,0) maternal_pm_absent_pregnant,
                IFNULL(l_reported.count_pm_absent_pregnant_mother,0) maternal_pm_absent_pregnant_mother,
                IFNULL(l_reported.count_present_maternal_status_none,0) maternal_present_none,
                IFNULL(l_reported.count_absent_maternal_status_none,0) maternal_absent_none,
                IFNULL(l_reported.count_disability_vision,0) disability_vision_learners,
                IFNULL(l_reported.count_disability_hearing,0) disability_hearing_learners,
                IFNULL(l_reported.count_disability_mobility,0) disability_mobility_learners,
                IFNULL(l_reported.count_disability_cognition,0) disability_cognition_learners,
                IFNULL(l_reported.count_disability_selfcare,0) disability_selfcare_learners,
                IFNULL(l_reported.count_disability_communication,0) disability_communication_learners,
                IFNULL(l_reported.count_absent_disability_vision,0) disability_vision_absent,
                IFNULL(l_reported.count_absent_disability_hearing,0) disability_hearing_absent,
                IFNULL(l_reported.count_absent_disability_mobility,0) disability_mobility_absent,
                IFNULL(l_reported.count_absent_disability_cognition,0) disability_cognition_absent,
                IFNULL(l_reported.count_absent_disability_selfcare,0) disability_selfcare_absent,
                IFNULL(l_reported.count_absent_disability_communication,0) disability_communication_absent,
                IFNULL(l_reported.count_absent_disability_other_condition,0) disability_other_condition_absent,
                IFNULL(l_reported.count_absent_disability_no_difficulty,0) disability_absent_disability_no_difficulty,
                IFNULL(l_reported.count_absent_disability_none,0) disability_absent_disability_none
        ";
    }

    public static function setCacheAttendanceBySchoolDateOnDuplicateColumns(){
        return "
            school_name = VALUES(school_name),
            lng = VALUES(lng),
            lat = VALUES(lat),
            district_id = VALUES(district_id),
            chiefdom_id = VALUES(chiefdom_id),
            tablet_phone_number = VALUES(tablet_phone_number),
            school_leader_phone_number = VALUES(school_leader_phone_number),
            teachers_total = VALUES(teachers_total),
            learners_total = VALUES(learners_total),
            count_classrooms = VALUES(count_classrooms),
            teachers_reported = VALUES(teachers_reported),
            teachers_present = VALUES(teachers_present),
            teachers_late = VALUES(teachers_late),
            teachers_absent = VALUES(teachers_absent),
            learners_reported = VALUES(learners_reported),
            learners_male = VALUES(learners_male),
            learners_female = VALUES(learners_female),
            learners_present = VALUES(learners_present),
            learners_absent = VALUES(learners_absent),
            learners_male_present = VALUES(learners_male_present),
            learners_female_present = VALUES(learners_female_present),
            learners_male_absent = VALUES(learners_male_absent),
            learners_female_absent = VALUES(learners_female_absent),
            learners_am_present = VALUES(learners_am_present),
            learners_am_absent = VALUES(learners_am_absent),
            learners_pm_present = VALUES(learners_pm_present),
            learners_pm_absent = VALUES(learners_pm_absent),
            maternal_learners_mothers = VALUES(maternal_learners_mothers),
            maternal_learners_pregnant = VALUES(maternal_learners_pregnant),
            maternal_learners_pregnant_mother = VALUES(maternal_learners_pregnant_mother),
            maternal_learners_status_none = VALUES(maternal_learners_status_none),
            maternal_am_present_mother = VALUES(maternal_am_present_mother),
            maternal_am_present_pregnant = VALUES(maternal_am_present_pregnant),
            maternal_am_present_pregnant_mother = VALUES(maternal_am_present_pregnant_mother),
            maternal_am_absent_mother = VALUES(maternal_am_absent_mother),
            maternal_am_absent_pregnant = VALUES(maternal_am_absent_pregnant),
            maternal_am_absent_pregnant_mother = VALUES(maternal_am_absent_pregnant_mother),
            maternal_pm_present_mother = VALUES(maternal_pm_present_mother),
            maternal_pm_present_pregnant = VALUES(maternal_pm_present_pregnant),
            maternal_pm_present_pregnant_mother = VALUES(maternal_pm_present_pregnant_mother),
            maternal_pm_absent_mother = VALUES(maternal_pm_absent_mother),
            maternal_pm_absent_pregnant = VALUES(maternal_pm_absent_pregnant),
            maternal_pm_absent_pregnant_mother = VALUES(maternal_pm_absent_pregnant_mother),
            maternal_present_none = VALUES(maternal_present_none),
            maternal_absent_none = VALUES(maternal_absent_none),
            disability_vision_learners = VALUES(disability_vision_learners),
            disability_hearing_learners = VALUES(disability_hearing_learners),
            disability_mobility_learners = VALUES(disability_mobility_learners),
            disability_cognition_learners = VALUES(disability_cognition_learners),
            disability_selfcare_learners = VALUES(disability_selfcare_learners),
            disability_communication_learners = VALUES(disability_communication_learners),
            disability_vision_absent = VALUES(disability_vision_absent),
            disability_hearing_absent = VALUES(disability_hearing_absent),
            disability_mobility_absent = VALUES(disability_mobility_absent),
            disability_cognition_absent = VALUES(disability_cognition_absent),
            disability_selfcare_absent = VALUES(disability_selfcare_absent),
            disability_communication_absent = VALUES(disability_communication_absent),
            disability_other_condition_absent = VALUES(disability_other_condition_absent),
            disability_absent_disability_no_difficulty = VALUES(disability_absent_disability_no_difficulty),
            disability_absent_disability_none = VALUES(disability_absent_disability_none)
        ";
    }

    public static function populateCacheSchoolInfoTable(){
        $sql="
        INSERT INTO cache_school_info
        SELECT * FROM (
            SELECT
                s.uuid,
                s.name,
                IFNULL(s.lat,0) lat,
                IFNULL(s.lng,0) lng,
                s.district_id,
                s.chiefdom_id,
                gd.name district_name,
                gc.name chiefdom_name,
                s_leader.school_leader_name,
                s_leader.phone_number school_leader_phone_number,
                s.tablet_phone_number,
				classroom.count_classrooms,
				teacher_attendance.last_teacher_submitted_date,
                teacher_attendance.last_teacher_submitted_date_formated,
                learner_attendance.last_learner_submitted_date_formated,
                IFNULL(s_teachers.count,0) teacher_total,
                IFNULL(s_teachers.count_females,0) teacher_female,
                IFNULL(s_teachers.count_males,0) teacher_male,
                t_profile.teacher_profile_role,
                t_profile.teacher_profile_start_date,
                t_profile.teacher_profile_date_of_birth,
                t_profile.teacher_profile_photo,
                t_profile.teacher_fingerprint_registration,
                t_profile.teacher_profile_phone_number,
                t_profile.teacher_profile_email,
                t_profile.teacher_profile_address,
                t_profile.teacher_profile_nin,
                t_profile.teacher_profile_required_fields_complete,
                IFNULL(s_learners.count,0) learners_total,
                IFNULL(s_learners.count_females,0) learner_female,
                IFNULL(s_learners.count_males,0) learner_male,
                IFNULL(s_learners.count_no_status,0) maternal_status_not_complete,
                IFNULL(s_learners.count_none,0) maternal_status_none,
                IFNULL(s_learners.count_mother, 0) maternal_status_mother,
                IFNULL(s_learners.count_pregnant,0) maternal_status_pregnant,
                IFNULL(s_learners.count_pregnant_mother,0) maternal_status_pregnant_mother,
                l_profile.learner_profile_date_of_birth,
                l_profile.learner_profile_sex,
                l_profile.learner_profile_nin,
                l_profile.learner_profile_admission_number,
                l_profile.learner_profile_strongest_language,
                l_profile.learner_profile_maternal_status,
                l_profile.learner_profile_complete_needs_assessment,
                l_profile.learner_profile_guardian_name,
                l_profile.learner_profile_guardian_phone,
                l_profile.learner_profile_guardian_address,
                l_profile.learner_profile_required_fields_complete,
                IFNULL(s_learners.count_special_needs_learners,0) learners_screened_special_needs,
                IFNULL(s_learners.count_disability_learners,0) disability_learners_total,
                IFNULL(s_learners.count_disability_vision,0) learners_disability_vision,
                IFNULL(s_learners.count_disability_vision_severity_1,0) learners_disability_vision_severity_1,
                IFNULL(s_learners.count_disability_vision_severity_2,0) learners_disability_vision_severity_2,
                IFNULL(s_learners.count_disability_vision_severity_3,0) learners_disability_vision_severity_3,
                IFNULL(s_learners.count_disability_hearing,0) learners_disability_hearing,
                IFNULL(s_learners.count_disability_hearing_severity_1,0) learners_disability_hearing_severity_1,
                IFNULL(s_learners.count_disability_hearing_severity_2,0) learners_disability_hearing_severity_2,
                IFNULL(s_learners.count_disability_hearing_severity_3,0) learners_disability_hearing_severity_3,
                IFNULL(s_learners.count_disability_mobility,0) learners_disability_mobility,
                IFNULL(s_learners.count_disability_mobility_severity_1,0) learners_disability_mobility_severity_1,
                IFNULL(s_learners.count_disability_mobility_severity_2,0) learners_disability_mobility_severity_2,
                IFNULL(s_learners.count_disability_mobility_severity_3,0) learners_disability_mobility_severity_3,
                IFNULL(s_learners.count_disability_cognition,0) learners_disability_cognition,
                IFNULL(s_learners.count_disability_cognition_severity_1,0) learners_disability_cognition_severity_1,
                IFNULL(s_learners.count_disability_cognition_severity_2,0) learners_disability_cognition_severity_2,
                IFNULL(s_learners.count_disability_cognition_severity_3,0) learners_disability_cognition_severity_3,
                IFNULL(s_learners.count_disability_selfcare,0) learners_disability_selfcare,
                IFNULL(s_learners.count_disability_selfcare_severity_1,0) learners_disability_selfcare_severity_1,
                IFNULL(s_learners.count_disability_selfcare_severity_2,0) learners_disability_selfcare_severity_2,
                IFNULL(s_learners.count_disability_selfcare_severity_3,0) learners_disability_selfcare_severity_3,
                IFNULL(s_learners.count_disability_communication,0) learners_disability_communication,
                IFNULL(s_learners.count_disability_communication_severity_1,0) learners_disability_communication_severity_1,
                IFNULL(s_learners.count_disability_communication_severity_2,0) learners_disability_communication_severity_2,
                IFNULL(s_learners.count_disability_communication_severity_3,0) learners_disability_communication_severity_3,
                IFNULL(s_learners.count_condition_albinism,0) learners_condition_albinism,
                IFNULL(s_learners.count_condition_epilepsy,0) learners_condition_epilepsy,
                IFNULL(s_learners.count_condition_dwarfism,0) learners_condition_dwarfism,
                IFNULL(removed_learners.count_end_reason_graduated,0) learners_removed_graduated,
                IFNULL(removed_learners.count_end_reason_transfer,0) learners_removed_transfer,
                IFNULL(removed_learners.count_end_reason_dropout_exams ,0) learners_removed_dropout_exams,
				IFNULL(removed_learners.count_end_reason_dropout_maternal,0) learners_removed_dropout_maternal,
				IFNULL(removed_learners.count_end_reason_dropout_other,0) learners_removed_dropout_other,
				IFNULL(removed_learners.count_end_reason_unknown,0) learners_removed_unknown,
                IFNULL(removed_learners.count_end_reason_duplicate,0) learners_removed_duplicate,
                IFNULL(removed_learners.count_end_reason_mistake,0) learners_removed_mistake
            FROM school s
            LEFT JOIN district_office do ON s.district_id = do.district_id
            LEFT JOIN geo gd ON gd.id = s.district_id and gd.type = 2
            LEFT JOIN geo gc ON gc.id = s.chiefdom_id and gc.type = 3
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
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(uuid) count_classrooms
                FROM school_group sg
                WHERE sg.deleted_at IS NULL
                GROUP BY school_uuid
            ) classroom ON classroom.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    MAX(date) last_teacher_submitted_date,
                    DATE_FORMAT(MAX(date), '%a %D  %b %Y') last_teacher_submitted_date_formated
                FROM person_attendance
                WHERE entity_type_oid = 'teacher'
                    AND submitted = 1
                    AND deleted_at IS NULL
                GROUP BY school_uuid
            ) teacher_attendance ON teacher_attendance.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    DATE_FORMAT(MAX(date), '%a %D  %b %Y') last_learner_submitted_date_formated
                FROM person_attendance
                WHERE entity_type_oid = 'learner'
                    AND submitted = 1
                    AND deleted_at IS NULL
                GROUP BY school_uuid
            ) learner_attendance ON learner_attendance.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count,
                    SUM(IF(p.sex_oid = 'female', 1, 0)) count_females,
                    SUM(IF(p.sex_oid = 'male', 1, 0)) count_males
                FROM teacher t
                LEFT JOIN person p ON p.uuid = t.person_uuid
                WHERE t.deleted_at IS NULL
                GROUP BY school_uuid
            ) s_teachers ON s_teachers.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    t.school_uuid,
                    COUNT(t.teacher_role_oid) AS teacher_profile_role,
                    COUNT(t.start_date) AS teacher_profile_start_date,
                    COUNT(p.date_of_birth) AS teacher_profile_date_of_birth,
                    COUNT(p.portrait_uuid) AS teacher_profile_photo,
                    SUM(CASE
                        WHEN p.fp_li_uuid IS NULL AND p.fp_lt_uuid IS NULL
                        AND p.fp_ri_uuid IS NULL AND p.fp_rt_uuid IS NULL
                        THEN 0 ELSE 1 END) AS teacher_fingerprint_registration,
                    SUM(CASE WHEN p.phone_1 IS NULL AND p.phone_2 IS NULL THEN 0 ELSE 1 END) AS teacher_profile_phone_number,
                    COUNT(p.email) AS teacher_profile_email,
                    COUNT(p.address) AS teacher_profile_address,
                    COUNT(p.nin) AS teacher_profile_nin,
                    COUNT(t.person_uuid) AS count_teacher,
                    SUM(CASE
                        WHEN t.teacher_role_oid IS NULL OR t.start_date IS NULL
                        OR p.date_of_birth IS NULL OR p.sex_oid IS NULL
                        OR p.portrait_uuid IS NULL OR p.address IS NULL
                        OR p.first_name IS NULL OR p.last_name IS NULL
                        THEN 0 ELSE 1 END) AS teacher_profile_required_fields_complete
                FROM teacher t
                LEFT JOIN person p ON t.person_uuid = p.uuid
                WHERE t.deleted_at IS NULL
                GROUP BY t.school_uuid
            ) t_profile ON t_profile.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count,
                    SUM(IF(p.sex_oid = 'female', 1, 0)) count_females,
                    SUM(IF(p.sex_oid = 'male', 1, 0)) count_males,
                    SUM(IF(l.maternal_status_oid = 'moth' AND p.sex_oid = 'female', 1, 0)) count_mother,
                    SUM(IF(l.maternal_status_oid = 'preg' AND p.sex_oid = 'female', 1, 0)) count_pregnant,
                    SUM(IF(l.maternal_status_oid = 'preg_moth' AND p.sex_oid = 'female', 1, 0)) count_pregnant_mother,
                    SUM(IF(l.maternal_status_oid = 'none' AND p.sex_oid = 'female', 1, 0)) count_none,
                    SUM(IF(l.maternal_status_oid IS NULL AND p.sex_oid = 'female', 1, 0)) count_no_status,
                    SUM(IF(l.disability_severity_oid_vision IS NOT NULL 
							AND l.disability_severity_oid_vision != '0_no_difficulty'
                            OR l.disability_severity_oid_hearing != '0_no_difficulty'
                            OR l.disability_severity_oid_mobility != '0_no_difficulty'
                            OR l.disability_severity_oid_cognition != '0_no_difficulty'
                            OR l.disability_severity_oid_selfcare != '0_no_difficulty'
                            OR l.disability_severity_oid_communication != '0_no_difficulty'
                            OR l.disability_other_condition_oid IS NOT NULL
                            , 1, 0)) count_special_needs_learners,
                    SUM(IF(l.disability_severity_oid_vision IS NOT NULL 
							AND l.disability_severity_oid_vision != '0_no_difficulty'
                            OR l.disability_severity_oid_hearing != '0_no_difficulty'
                            OR l.disability_severity_oid_mobility != '0_no_difficulty'
                            OR l.disability_severity_oid_cognition != '0_no_difficulty'
                            OR l.disability_severity_oid_selfcare != '0_no_difficulty'
                            OR l.disability_severity_oid_communication != '0_no_difficulty'
                            , 1, 0)) count_disability_learners,
                    SUM(IF(l.disability_severity_oid_vision IS NOT NULL AND l.disability_severity_oid_vision != '0_no_difficulty', 1, 0)) count_disability_vision,
                    SUM(IF(l.disability_severity_oid_vision = '1_some_difficulty', 1, 0)) count_disability_vision_severity_1,
                    SUM(IF(l.disability_severity_oid_vision = '2_lot_of_difficulty', 1, 0)) count_disability_vision_severity_2,
                    SUM(IF(l.disability_severity_oid_vision = '3_cannot_do', 1, 0)) count_disability_vision_severity_3,
                    SUM(IF(l.disability_severity_oid_hearing IS NOT NULL AND l.disability_severity_oid_hearing != '0_no_difficulty', 1, 0)) count_disability_hearing,
                    SUM(IF(l.disability_severity_oid_hearing = '1_some_difficulty', 1, 0)) count_disability_hearing_severity_1,
                    SUM(IF(l.disability_severity_oid_hearing = '2_lot_of_difficulty', 1, 0)) count_disability_hearing_severity_2,
                    SUM(IF(l.disability_severity_oid_hearing = '3_cannot_do', 1, 0)) count_disability_hearing_severity_3,
                    SUM(IF(l.disability_severity_oid_mobility IS NOT NULL AND l.disability_severity_oid_mobility != '0_no_difficulty', 1, 0)) count_disability_mobility,
                    SUM(IF(l.disability_severity_oid_mobility = '1_some_difficulty', 1, 0)) count_disability_mobility_severity_1,
                    SUM(IF(l.disability_severity_oid_mobility = '2_lot_of_difficulty', 1, 0)) count_disability_mobility_severity_2,
                    SUM(IF(l.disability_severity_oid_mobility = '3_cannot_do', 1, 0)) count_disability_mobility_severity_3,
                    SUM(IF(l.disability_severity_oid_cognition IS NOT NULL AND l.disability_severity_oid_cognition != '0_no_difficulty', 1, 0)) count_disability_cognition,
                    SUM(IF(l.disability_severity_oid_cognition = '1_some_difficulty', 1, 0)) count_disability_cognition_severity_1,
                    SUM(IF(l.disability_severity_oid_cognition = '2_lot_of_difficulty', 1, 0)) count_disability_cognition_severity_2,
                    SUM(IF(l.disability_severity_oid_cognition = '3_cannot_do', 1, 0)) count_disability_cognition_severity_3,
                    SUM(IF(l.disability_severity_oid_selfcare IS NOT NULL AND l.disability_severity_oid_selfcare != '0_no_difficulty', 1, 0)) count_disability_selfcare,
                    SUM(IF(l.disability_severity_oid_selfcare = '1_some_difficulty', 1, 0)) count_disability_selfcare_severity_1,
                    SUM(IF(l.disability_severity_oid_selfcare = '2_lot_of_difficulty', 1, 0)) count_disability_selfcare_severity_2,
                    SUM(IF(l.disability_severity_oid_selfcare = '3_cannot_do', 1, 0)) count_disability_selfcare_severity_3,
                    SUM(IF(l.disability_severity_oid_communication IS NOT NULL AND l.disability_severity_oid_communication != '0_no_difficulty', 1, 0)) count_disability_communication,
                    SUM(IF(l.disability_severity_oid_communication = '1_some_difficulty', 1, 0)) count_disability_communication_severity_1,
                    SUM(IF(l.disability_severity_oid_communication = '2_lot_of_difficulty', 1, 0)) count_disability_communication_severity_2,
                    SUM(IF(l.disability_severity_oid_communication = '3_cannot_do', 1, 0)) count_disability_communication_severity_3,
                    SUM(IF(l.disability_other_condition_oid = 'albinism', 1, 0)) count_condition_albinism,
                    SUM(IF(l.disability_other_condition_oid = 'epilepsy', 1, 0)) count_condition_epilepsy,
                    SUM(IF(l.disability_other_condition_oid = 'dwarfism', 1, 0)) count_condition_dwarfism,
                    SUM(IF(sla.end_reason_learner_oid = 'graduated',1,0)) count_end_reason_graduated,
					SUM(IF(sla.end_reason_learner_oid = 'transfer',1,0)) count_end_reason_transfer,
                    SUM(IF(sla.end_reason_learner_oid = 'dropout_exams',1,0)) count_end_reason_dropout_exams,
                    SUM(IF(sla.end_reason_learner_oid = 'dropout_maternal',1,0)) count_end_reason_dropout_maternal,
                    SUM(IF(sla.end_reason_learner_oid = 'dropout_other',1,0)) count_end_reason_dropout_other,
                    SUM(IF(sla.end_reason_learner_oid = 'unknown',1,0)) count_end_reason_unknown,
                    SUM(IF(sla.end_reason_learner_oid = 'duplicate',1,0)) count_end_reason_duplicate,
                    SUM(IF(sla.end_reason_learner_oid = 'mistake',1,0)) count_end_reason_mistake
                FROM school_learner_admission sla
                LEFT JOIN learner l on sla.learner_uuid = l.uuid
                LEFT JOIN person p ON p.uuid = l.person_uuid
                WHERE sla.deleted_at IS NULL
                    AND (l.learner_id IS NOT NULL AND l.learner_id != '')
                GROUP BY school_uuid
            ) s_learners ON s_learners.school_uuid = s.uuid
            LEFT JOIN(
                SELECT
                    school_uuid,
                    SUM(IF(sla.end_reason_learner_oid = 'graduated',1,0)) count_end_reason_graduated,
                    SUM(IF(sla.end_reason_learner_oid = 'transfer',1,0)) count_end_reason_transfer,
                    SUM(IF(sla.end_reason_learner_oid = 'dropout_exams',1,0)) count_end_reason_dropout_exams,
                    SUM(IF(sla.end_reason_learner_oid = 'dropout_maternal',1,0)) count_end_reason_dropout_maternal,
                    SUM(IF(sla.end_reason_learner_oid = 'dropout_other',1,0)) count_end_reason_dropout_other,
                    SUM(IF(sla.end_reason_learner_oid = 'unknown',1,0)) count_end_reason_unknown,
                    SUM(IF(sla.end_reason_learner_oid = 'duplicate',1,0)) count_end_reason_duplicate,
                    SUM(IF(sla.end_reason_learner_oid = 'mistake',1,0)) count_end_reason_mistake
                FROM school_learner_admission sla
                GROUP BY school_uuid
            ) removed_learners ON removed_learners.school_uuid = s.uuid
            LEFT JOIN (
                 SELECT
                    sla.school_uuid,
                    COUNT(p.date_of_birth) AS learner_profile_date_of_birth,
                    COUNT(p.sex_oid) AS learner_profile_sex,
                    COUNT(p.nin) AS learner_profile_nin,
                    COUNT(sla.admission_number) AS learner_profile_admission_number,
                    COUNT(l.language_oid_strongest) AS learner_profile_strongest_language,
                    SUM(CASE
                        WHEN (l.maternal_status_oid IS NULL AND p.sex_oid = 'female') OR
                        (l.maternal_status_oid IS NULL AND p.sex_oid IS NULL) THEN 0
                        ELSE 1 END) AS learner_profile_maternal_status,
                    SUM(CASE
                        WHEN l.disability_severity_oid_vision IS NULL
                            OR l.disability_severity_oid_hearing IS NULL
                            OR l.disability_severity_oid_mobility IS NULL
                            OR l.disability_severity_oid_cognition IS NULL
                            OR l.disability_severity_oid_selfcare IS NULL
                            OR l.disability_severity_oid_communication IS NULL
                            OR disability_other_condition_oid IS NULL
                        THEN 0
                        ELSE 1 END) AS learner_profile_complete_needs_assessment,
                    COUNT(gp.first_name ) AS learner_profile_guardian_name,
                    SUM(CASE WHEN gp.phone_1 IS NULL AND gp.phone_2 IS NULL THEN 0 ELSE 1 END) AS learner_profile_guardian_phone,
                    COUNT(gp.address) AS learner_profile_guardian_address,
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
                        ELSE 1 END) AS learner_profile_required_fields_complete
                FROM school_learner_admission sla
                LEFT JOIN learner l ON sla.learner_uuid = l.uuid
                LEFT JOIN person p ON l.person_uuid = p.uuid
                LEFT JOIN person gp ON l.guardian_person_uuid = gp.uuid
                WHERE l.deleted_by IS NULL
                GROUP BY sla.school_uuid
            ) l_profile ON l_profile.school_uuid = s.uuid
        ) AS si
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            lat = VALUES(lat),
            lng = VALUES(lng),
            district_id = VALUES(district_id),
            chiefdom_id = VALUES(chiefdom_id),
            district_name = VALUES(district_name),
            chiefdom_name = VALUES(chiefdom_name),
            school_leader_name = VALUES(school_leader_name),
            school_leader_phone_number = VALUES(school_leader_phone_number),
            tablet_phone_number = VALUES(tablet_phone_number),
            count_classrooms = VALUES(count_classrooms),
            last_teacher_submitted_date = VALUES(last_teacher_submitted_date),
            last_teacher_submitted_date_formated = VALUES(last_teacher_submitted_date_formated),
            last_learner_submitted_date_formated = VALUES(last_learner_submitted_date_formated),
            teacher_total = VALUES(teacher_total),
            teacher_female = VALUES(teacher_female),
            teacher_male = VALUES(teacher_male),
            teacher_profile_role = VALUES(teacher_profile_role),
            teacher_profile_start_date = VALUES(teacher_profile_start_date),
            teacher_profile_date_of_birth = VALUES(teacher_profile_date_of_birth),
            teacher_profile_photo = VALUES(teacher_profile_photo),
            teacher_profile_phone_number = VALUES(teacher_profile_phone_number),
            teacher_profile_email = VALUES(teacher_profile_email),
            teacher_profile_address = VALUES(teacher_profile_address),
            teacher_profile_nin = VALUES(teacher_profile_nin),
            teacher_profile_required_fields_complete = VALUES(teacher_profile_required_fields_complete),
            learners_total = VALUES(learners_total),
            learner_female = VALUES(learner_female),
            learner_male = VALUES(learner_male),
            maternal_status_not_complete = VALUES(maternal_status_not_complete),
            maternal_status_none = VALUES(maternal_status_none),
            maternal_status_mother = VALUES(maternal_status_mother),
            maternal_status_pregnant = VALUES(maternal_status_pregnant),
            maternal_status_pregnant_mother = VALUES(maternal_status_pregnant_mother),
            learner_profile_date_of_birth = VALUES(learner_profile_date_of_birth),
            learner_profile_sex = VALUES(learner_profile_sex),
            learner_profile_nin = VALUES(learner_profile_nin),
            learner_profile_admission_number = VALUES(learner_profile_admission_number),
            learner_profile_strongest_language = VALUES(learner_profile_strongest_language),
            learner_profile_maternal_status = VALUES(learner_profile_maternal_status),
            learner_profile_complete_needs_assessment = VALUES(learner_profile_complete_needs_assessment),
            learner_profile_guardian_name = VALUES(learner_profile_guardian_name),
            learner_profile_guardian_phone = VALUES(learner_profile_guardian_phone),
            learner_profile_guardian_address = VALUES(learner_profile_guardian_address),
            learner_profile_required_fields_complete = VALUES(learner_profile_required_fields_complete),
            learners_screened_special_needs = VALUES(learners_screened_special_needs),
            disability_learners_total = VALUES(disability_learners_total),
            learners_disability_vision = VALUES(learners_disability_vision),
            learners_disability_vision_severity_1 = VALUES(learners_disability_vision_severity_1),
            learners_disability_vision_severity_2 = VALUES(learners_disability_vision_severity_2),
            learners_disability_vision_severity_3 = VALUES(learners_disability_vision_severity_3),
            learners_disability_hearing = VALUES(learners_disability_hearing),
            learners_disability_hearing_severity_1 = VALUES(learners_disability_hearing_severity_1),
            learners_disability_hearing_severity_2 = VALUES(learners_disability_hearing_severity_2),
            learners_disability_hearing_severity_3 = VALUES(learners_disability_hearing_severity_3),
            learners_disability_mobility = VALUES(learners_disability_mobility),
            learners_disability_mobility_severity_1 = VALUES(learners_disability_mobility_severity_1),
            learners_disability_mobility_severity_2 = VALUES(learners_disability_mobility_severity_2),
            learners_disability_mobility_severity_3 = VALUES(learners_disability_mobility_severity_3),
            learners_disability_cognition = VALUES(learners_disability_cognition),
            learners_disability_cognition_severity_1 = VALUES(learners_disability_cognition_severity_1),
            learners_disability_cognition_severity_2 = VALUES(learners_disability_cognition_severity_2),
            learners_disability_cognition_severity_3 = VALUES(learners_disability_cognition_severity_3),
            learners_disability_selfcare = VALUES(learners_disability_selfcare),
            learners_disability_selfcare_severity_1 = VALUES(learners_disability_selfcare_severity_1),
            learners_disability_selfcare_severity_2 = VALUES(learners_disability_selfcare_severity_2),
            learners_disability_selfcare_severity_3 = VALUES(learners_disability_selfcare_severity_3),
            learners_disability_communication = VALUES(learners_disability_communication),
            learners_disability_communication_severity_1 = VALUES(learners_disability_communication_severity_1),
            learners_disability_communication_severity_2 = VALUES(learners_disability_communication_severity_2),
            learners_disability_communication_severity_3 = VALUES(learners_disability_communication_severity_3),
            learners_condition_albinism = VALUES(learners_condition_albinism),
            learners_condition_epilepsy = VALUES(learners_condition_epilepsy),
            learners_condition_dwarfism = VALUES(learners_condition_dwarfism),
            learners_removed_graduated = VALUES(learners_removed_graduated),
            learners_removed_transfer = VALUES(learners_removed_transfer),
            learners_removed_dropout_exams = VALUES(learners_removed_dropout_exams),
            learners_removed_dropout_maternal = VALUES(learners_removed_dropout_maternal),
            learners_removed_dropout_other = VALUES(learners_removed_dropout_other),
            learners_removed_unknown = VALUES(learners_removed_unknown),
            learners_removed_duplicate = VALUES(learners_removed_duplicate),
            learners_removed_mistake = VALUES(learners_removed_mistake)

        ";
        try {
            DB::statement($sql);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
        return true;
    }

    public static function populateCacheAttendanceByDate($date): bool{
        $selectColumns = self::setCacheAttendanceBySchoolDateColumns();
        $onDuplicateColumns = self::setCacheAttendanceBySchoolDateOnDuplicateColumns();
        $sql="
        INSERT INTO cache_attendance_by_school_date
        SELECT * FROM(
            SELECT
				{$selectColumns}
            FROM school s
            LEFT JOIN (
                SELECT
                    t.school_uuid,
                    MAX(IFNULL(p.phone_1,p.phone_2)) phone_number
                FROM teacher t
                LEFT JOIN person p ON p.uuid = t.person_uuid
                WHERE (teacher_role_oid = 'head_teacher' OR teacher_role_oid = 'vice_principal')
                GROUP BY school_uuid
            ) school_contact ON school_contact.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM teacher
                WHERE created_at < DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY)
                    AND (deleted_at IS NULL OR deleted_at >= DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY))
                GROUP BY school_uuid
            ) s_teachers ON s_teachers.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM school_learner_admission sla
                LEFT JOIN learner l on sla.learner_uuid = l.uuid
                WHERE sla.created_at < DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY)
                    AND (sla.deleted_at IS NULL OR sla.deleted_at >= DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY))
                    AND (l.learner_id IS NOT NULL AND l.learner_id != '')
                GROUP BY school_uuid
            ) s_learners ON s_learners.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(uuid) count_classrooms
                FROM school_group sg
                WHERE sg.deleted_at IS NULL
                GROUP BY school_uuid
            ) classroom ON classroom.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                    AND date = @date
                    AND attendance_status_oid = 'present'
                GROUP BY school_uuid
            ) as t_present on s.uuid = t_present.school_uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                    AND date = @date
                    AND attendance_status_oid = 'late'
                GROUP BY school_uuid
            ) as t_late on s.uuid = t_late.school_uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                    AND date = @date
                    AND attendance_status_oid = 'absent'
                GROUP BY school_uuid
            ) AS t_absent ON s.uuid = t_absent.school_uuid
            LEFT JOIN (
                SELECT
                    pa.school_uuid,
                    COUNT(pa.uuid) AS count,
                    SUM(CASE WHEN p.sex_oid = 'male' THEN 1 ELSE 0 END) AS count_male,
                    SUM(CASE WHEN p.sex_oid = 'female' THEN 1 ELSE 0 END) AS count_female,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' THEN 1 ELSE 0 END) AS count_am_present,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' THEN 1 ELSE 0 END) AS count_am_absent,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'present' THEN 1 ELSE 0 END) AS count_pm_present,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'absent' THEN 1 ELSE 0 END) AS count_pm_absent,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND pa.attendance_pm_status_oid = 'present' THEN 1 ELSE 0 END) AS count_present,
					SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent' THEN 1 ELSE 0 END) AS count_absent,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND pa.attendance_pm_status_oid = 'present' AND p.sex_oid = 'male' THEN 1 ELSE 0 END) AS count_male_present,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND pa.attendance_pm_status_oid = 'present' AND p.sex_oid = 'female' THEN 1 ELSE 0 END) AS count_female_present,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent' AND p.sex_oid = 'male' THEN 1 ELSE 0 END) AS count_male_absent,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent' AND p.sex_oid = 'female' THEN 1 ELSE 0 END) AS count_female_absent,
                    SUM(CASE WHEN l.maternal_status_oid = 'moth' THEN 1 ELSE 0 END) AS count_mother,
                    SUM(CASE WHEN l.maternal_status_oid = 'preg' THEN 1 ELSE 0 END) AS count_pregnant,
                    SUM(CASE WHEN l.maternal_status_oid = 'preg_moth' THEN 1 ELSE 0 END) AS count_pregnant_mother,
                    SUM(CASE WHEN l.maternal_status_oid = 'none' THEN 1 ELSE 0 END) AS count_status_none,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND l.maternal_status_oid = 'moth' THEN 1 ELSE 0 END) AS count_am_present_mother,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND l.maternal_status_oid = 'preg' THEN 1 ELSE 0 END) AS count_am_present_pregnant,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND l.maternal_status_oid = 'preg_moth' THEN 1 ELSE 0 END) AS count_am_present_pregnant_mother,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND l.maternal_status_oid = 'moth' THEN 1 ELSE 0 END) AS count_am_absent_mother,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND l.maternal_status_oid = 'preg' THEN 1 ELSE 0 END) AS count_am_absent_pregnant,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND l.maternal_status_oid = 'preg_moth' THEN 1 ELSE 0 END) AS count_am_absent_pregnant_mother,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'present' AND l.maternal_status_oid = 'moth' THEN 1 ELSE 0 END) AS count_pm_present_mother,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'present' AND l.maternal_status_oid = 'preg' THEN 1 ELSE 0 END) AS count_pm_present_pregnant,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'present' AND l.maternal_status_oid = 'preg_moth' THEN 1 ELSE 0 END) AS count_pm_present_pregnant_mother,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'absent' AND l.maternal_status_oid = 'moth' THEN 1 ELSE 0 END) AS count_pm_absent_mother,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'absent' AND l.maternal_status_oid = 'preg' THEN 1 ELSE 0 END) AS count_pm_absent_pregnant,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'absent' AND l.maternal_status_oid = 'preg_moth' THEN 1 ELSE 0 END) AS count_pm_absent_pregnant_mother,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND pa.attendance_pm_status_oid = 'present' AND l.maternal_status_oid = 'none'  AND p.sex_oid = 'female' THEN 1 ELSE 0 END) AS count_present_maternal_status_none,
					SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent' AND l.maternal_status_oid = 'none'  AND p.sex_oid = 'female' THEN 1 ELSE 0 END) AS count_absent_maternal_status_none,
                  SUM(IF(l.disability_severity_oid_vision IS NOT NULL AND l.disability_severity_oid_vision != '0_no_difficulty', 1, 0)) count_disability_vision,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
							 AND pa.attendance_pm_status_oid = 'absent' 
                             AND l.disability_severity_oid_vision IS NOT NULL 
                             AND l.disability_severity_oid_vision != '0_no_difficulty' 
                             THEN 1 ELSE 0 END) AS count_absent_disability_vision,
					SUM(IF(l.disability_severity_oid_hearing IS NOT NULL AND l.disability_severity_oid_hearing != '0_no_difficulty', 1, 0)) count_disability_hearing,
					SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
							 AND pa.attendance_pm_status_oid = 'absent' 
                             AND l.disability_severity_oid_hearing IS NOT NULL 
                             AND l.disability_severity_oid_hearing != '0_no_difficulty' 
                             THEN 1 ELSE 0 END) AS count_absent_disability_hearing,
					SUM(IF(l.disability_severity_oid_mobility IS NOT NULL AND l.disability_severity_oid_mobility != '0_no_difficulty', 1, 0)) count_disability_mobility,
					SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
							 AND pa.attendance_pm_status_oid = 'absent' 
                             AND l.disability_severity_oid_mobility IS NOT NULL 
                             AND l.disability_severity_oid_mobility != '0_no_difficulty' 
                             THEN 1 ELSE 0 END) AS count_absent_disability_mobility,
					SUM(IF(l.disability_severity_oid_cognition IS NOT NULL AND l.disability_severity_oid_cognition != '0_no_difficulty', 1, 0)) count_disability_cognition,
					SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
							 AND pa.attendance_pm_status_oid = 'absent' 
                             AND l.disability_severity_oid_cognition IS NOT NULL 
                             AND l.disability_severity_oid_cognition != '0_no_difficulty' 
                             THEN 1 ELSE 0 END) AS count_absent_disability_cognition,
					SUM(IF(l.disability_severity_oid_selfcare IS NOT NULL AND l.disability_severity_oid_selfcare != '0_no_difficulty', 1, 0)) count_disability_selfcare,
					SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
							 AND pa.attendance_pm_status_oid = 'absent' 
                             AND l.disability_severity_oid_selfcare IS NOT NULL 
                             AND l.disability_severity_oid_selfcare != '0_no_difficulty' 
                             THEN 1 ELSE 0 END) AS count_absent_disability_selfcare,
					SUM(IF(l.disability_severity_oid_communication IS NOT NULL AND l.disability_severity_oid_communication != '0_no_difficulty', 1, 0)) count_disability_communication,
					SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
							 AND pa.attendance_pm_status_oid = 'absent' 
                             AND l.disability_severity_oid_communication IS NOT NULL 
                             AND l.disability_severity_oid_communication != '0_no_difficulty' 
                             THEN 1 ELSE 0 END) AS count_absent_disability_communication,
					SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
							 AND pa.attendance_pm_status_oid = 'absent' 
                             AND l.disability_other_condition_oid IS NOT NULL 
                             AND l.disability_other_condition_oid != 'none' 
                             THEN 1 ELSE 0 END) AS count_absent_disability_other_condition,
					SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
							 AND pa.attendance_pm_status_oid = 'absent' 
                             AND l.disability_severity_oid_vision = '0_no_difficulty' 
                             AND l.disability_severity_oid_hearing = '0_no_difficulty' 
                             AND l.disability_severity_oid_mobility = '0_no_difficulty' 
                             AND l.disability_severity_oid_cognition = '0_no_difficulty' 
                             AND l.disability_severity_oid_selfcare = '0_no_difficulty' 
                             AND l.disability_severity_oid_communication = '0_no_difficulty' 
                             THEN 1 ELSE 0 END) AS count_absent_disability_no_difficulty,
					SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
							 AND pa.attendance_pm_status_oid = 'absent' 
                             AND l.disability_severity_oid_vision IS NULL
                             AND l.disability_severity_oid_hearing IS NULL
                             AND l.disability_severity_oid_mobility IS NULL
                             AND l.disability_severity_oid_cognition IS NULL
                             AND l.disability_severity_oid_selfcare IS NULL
                             AND l.disability_severity_oid_communication IS NULL 
                             THEN 1 ELSE 0 END) AS count_absent_disability_none
                FROM person_attendance pa
                LEFT JOIN learner l ON l.person_uuid = pa.person_uuid
                LEFT JOIN person p ON p.uuid = pa.person_uuid
                WHERE pa.submitted = 1
                    AND pa.deleted_at IS NULL
                    AND pa.entity_type_oid = 'learner'
                    AND pa.date = @date
                GROUP BY pa.school_uuid
            ) AS l_reported ON s.uuid = l_reported.school_uuid
            WHERE s.created_at < DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY)
                AND (s.deleted_at IS NULL OR s.deleted_at >= DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY))
        )ca
        ON DUPLICATE KEY UPDATE
         {$onDuplicateColumns}
        ";

        try {
            $sql = str_replace("@date","'$date'",$sql);
            DB::statement($sql);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
        return true;
    }

    public static function populateCacheAttendanceBySchoolAndDate($schoolUuid,$date): bool{
        $selectColumns = self::setCacheAttendanceBySchoolDateColumns();
        $onDuplicateColumns = self::setCacheAttendanceBySchoolDateOnDuplicateColumns();
        $sql="
        INSERT INTO cache_attendance_by_school_date
        SELECT * FROM(
            SELECT
                {$selectColumns}
            FROM school s
            LEFT JOIN (
                SELECT
                    t.school_uuid,
                    MAX(IFNULL(p.phone_1,p.phone_2)) phone_number
                FROM teacher t
                LEFT JOIN person p ON p.uuid = t.person_uuid
                WHERE (teacher_role_oid = 'head_teacher' OR teacher_role_oid = 'vice_principal')
                GROUP BY school_uuid
            ) school_contact ON school_contact.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM teacher
                WHERE created_at < DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY)
                    AND (deleted_at IS NULL OR deleted_at >= DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY))
                GROUP BY school_uuid
            ) s_teachers ON s_teachers.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM school_learner_admission sla
                LEFT JOIN learner l on sla.learner_uuid = l.uuid
                WHERE sla.created_at < DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY)
                    AND (sla.deleted_at IS NULL OR sla.deleted_at >= DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY))
                    AND (l.learner_id IS NOT NULL AND l.learner_id != '')
                GROUP BY school_uuid
            ) s_learners ON s_learners.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(uuid) count_classrooms
                FROM school_group sg
                WHERE sg.deleted_at IS NULL
                GROUP BY school_uuid
            ) classroom ON classroom.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                    AND date = @date
                    AND attendance_status_oid = 'present'
                GROUP BY school_uuid
            ) as t_present on s.uuid = t_present.school_uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                    AND date = @date
                    AND attendance_status_oid = 'late'
                GROUP BY school_uuid
            ) as t_late on s.uuid = t_late.school_uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                    AND date = @date
                    AND attendance_status_oid = 'absent'
                GROUP BY school_uuid
            ) AS t_absent ON s.uuid = t_absent.school_uuid
            LEFT JOIN (
                SELECT
                    pa.school_uuid,
                    COUNT(pa.uuid) AS count,
                    SUM(CASE WHEN p.sex_oid = 'male' THEN 1 ELSE 0 END) AS count_male,
                    SUM(CASE WHEN p.sex_oid = 'female' THEN 1 ELSE 0 END) AS count_female,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' THEN 1 ELSE 0 END) AS count_am_present,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' THEN 1 ELSE 0 END) AS count_am_absent,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'present' THEN 1 ELSE 0 END) AS count_pm_present,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'absent' THEN 1 ELSE 0 END) AS count_pm_absent,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND pa.attendance_pm_status_oid = 'present' THEN 1 ELSE 0 END) AS count_present,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent' THEN 1 ELSE 0 END) AS count_absent,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND pa.attendance_pm_status_oid = 'present' AND p.sex_oid = 'male' THEN 1 ELSE 0 END) AS count_male_present,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND pa.attendance_pm_status_oid = 'present' AND p.sex_oid = 'female' THEN 1 ELSE 0 END) AS count_female_present,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent' AND p.sex_oid = 'male' THEN 1 ELSE 0 END) AS count_male_absent,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent' AND p.sex_oid = 'female' THEN 1 ELSE 0 END) AS count_female_absent,
                    SUM(CASE WHEN l.maternal_status_oid = 'moth' THEN 1 ELSE 0 END) AS count_mother,
                    SUM(CASE WHEN l.maternal_status_oid = 'preg' THEN 1 ELSE 0 END) AS count_pregnant,
                    SUM(CASE WHEN l.maternal_status_oid = 'preg_moth' THEN 1 ELSE 0 END) AS count_pregnant_mother,
                    SUM(CASE WHEN l.maternal_status_oid = 'none' THEN 1 ELSE 0 END) AS count_status_none,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND l.maternal_status_oid = 'moth' THEN 1 ELSE 0 END) AS count_am_present_mother,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND l.maternal_status_oid = 'preg' THEN 1 ELSE 0 END) AS count_am_present_pregnant,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND l.maternal_status_oid = 'preg_moth' THEN 1 ELSE 0 END) AS count_am_present_pregnant_mother,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND l.maternal_status_oid = 'moth' THEN 1 ELSE 0 END) AS count_am_absent_mother,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND l.maternal_status_oid = 'preg' THEN 1 ELSE 0 END) AS count_am_absent_pregnant,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND l.maternal_status_oid = 'preg_moth' THEN 1 ELSE 0 END) AS count_am_absent_pregnant_mother,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'present' AND l.maternal_status_oid = 'moth' THEN 1 ELSE 0 END) AS count_pm_present_mother,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'present' AND l.maternal_status_oid = 'preg' THEN 1 ELSE 0 END) AS count_pm_present_pregnant,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'present' AND l.maternal_status_oid = 'preg_moth' THEN 1 ELSE 0 END) AS count_pm_present_pregnant_mother,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'absent' AND l.maternal_status_oid = 'moth' THEN 1 ELSE 0 END) AS count_pm_absent_mother,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'absent' AND l.maternal_status_oid = 'preg' THEN 1 ELSE 0 END) AS count_pm_absent_pregnant,
                    SUM(CASE WHEN pa.attendance_pm_status_oid = 'absent' AND l.maternal_status_oid = 'preg_moth' THEN 1 ELSE 0 END) AS count_pm_absent_pregnant_mother,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'present' AND pa.attendance_pm_status_oid = 'present' AND l.maternal_status_oid = 'none'  AND p.sex_oid = 'female' THEN 1 ELSE 0 END) AS count_present_maternal_status_none,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent' AND l.maternal_status_oid = 'none'  AND p.sex_oid = 'female' THEN 1 ELSE 0 END) AS count_absent_maternal_status_none,
                    SUM(IF(l.disability_severity_oid_vision IS NOT NULL AND l.disability_severity_oid_vision != '0_no_difficulty', 1, 0)) count_disability_vision,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
                            AND pa.attendance_pm_status_oid = 'absent' 
                            AND l.disability_severity_oid_vision IS NOT NULL 
                            AND l.disability_severity_oid_vision != '0_no_difficulty' 
                            THEN 1 ELSE 0 END) AS count_absent_disability_vision,
                    SUM(IF(l.disability_severity_oid_hearing IS NOT NULL AND l.disability_severity_oid_hearing != '0_no_difficulty', 1, 0)) count_disability_hearing,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
                            AND pa.attendance_pm_status_oid = 'absent' 
                            AND l.disability_severity_oid_hearing IS NOT NULL 
                            AND l.disability_severity_oid_hearing != '0_no_difficulty' 
                            THEN 1 ELSE 0 END) AS count_absent_disability_hearing,
                    SUM(IF(l.disability_severity_oid_mobility IS NOT NULL AND l.disability_severity_oid_mobility != '0_no_difficulty', 1, 0)) count_disability_mobility,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
                            AND pa.attendance_pm_status_oid = 'absent' 
                            AND l.disability_severity_oid_mobility IS NOT NULL 
                            AND l.disability_severity_oid_mobility != '0_no_difficulty' 
                            THEN 1 ELSE 0 END) AS count_absent_disability_mobility,
                    SUM(IF(l.disability_severity_oid_cognition IS NOT NULL AND l.disability_severity_oid_cognition != '0_no_difficulty', 1, 0)) count_disability_cognition,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
                            AND pa.attendance_pm_status_oid = 'absent' 
                            AND l.disability_severity_oid_cognition IS NOT NULL 
                            AND l.disability_severity_oid_cognition != '0_no_difficulty' 
                            THEN 1 ELSE 0 END) AS count_absent_disability_cognition,
                    SUM(IF(l.disability_severity_oid_selfcare IS NOT NULL AND l.disability_severity_oid_selfcare != '0_no_difficulty', 1, 0)) count_disability_selfcare,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
                            AND pa.attendance_pm_status_oid = 'absent' 
                            AND l.disability_severity_oid_selfcare IS NOT NULL 
                            AND l.disability_severity_oid_selfcare != '0_no_difficulty' 
                            THEN 1 ELSE 0 END) AS count_absent_disability_selfcare,
                    SUM(IF(l.disability_severity_oid_communication IS NOT NULL AND l.disability_severity_oid_communication != '0_no_difficulty', 1, 0)) count_disability_communication,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
                            AND pa.attendance_pm_status_oid = 'absent' 
                            AND l.disability_severity_oid_communication IS NOT NULL 
                            AND l.disability_severity_oid_communication != '0_no_difficulty' 
                            THEN 1 ELSE 0 END) AS count_absent_disability_communication,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
                            AND pa.attendance_pm_status_oid = 'absent' 
                            AND l.disability_other_condition_oid IS NOT NULL 
                            AND l.disability_other_condition_oid != 'none' 
                            THEN 1 ELSE 0 END) AS count_absent_disability_other_condition,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
                            AND pa.attendance_pm_status_oid = 'absent' 
                            AND l.disability_severity_oid_vision = '0_no_difficulty' 
                            AND l.disability_severity_oid_hearing = '0_no_difficulty' 
                            AND l.disability_severity_oid_mobility = '0_no_difficulty' 
                            AND l.disability_severity_oid_cognition = '0_no_difficulty' 
                            AND l.disability_severity_oid_selfcare = '0_no_difficulty' 
                            AND l.disability_severity_oid_communication = '0_no_difficulty' 
                            THEN 1 ELSE 0 END) AS count_absent_disability_no_difficulty,
                    SUM(CASE WHEN pa.attendance_am_status_oid = 'absent' 
                            AND pa.attendance_pm_status_oid = 'absent' 
                            AND l.disability_severity_oid_vision IS NULL
                            AND l.disability_severity_oid_hearing IS NULL
                            AND l.disability_severity_oid_mobility IS NULL
                            AND l.disability_severity_oid_cognition IS NULL
                            AND l.disability_severity_oid_selfcare IS NULL
                            AND l.disability_severity_oid_communication IS NULL 
                            THEN 1 ELSE 0 END) AS count_absent_disability_none
                FROM person_attendance pa
                LEFT JOIN learner l ON l.person_uuid = pa.person_uuid
                WHERE pa.submitted = 1
                    AND pa.deleted_at IS NULL
                    AND pa.entity_type_oid = 'learner'
                    AND pa.date = @date
                GROUP BY pa.school_uuid
            ) AS l_reported ON s.uuid = l_reported.school_uuid
            WHERE s.uuid = '{$schoolUuid}'
        )ca
        ON DUPLICATE KEY UPDATE
            {$onDuplicateColumns}

        ";

        try {
            $sql = str_replace("@date","'$date'",$sql);
            DB::statement($sql);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
        return true;
    }

    public static function initialPopulateCacheAttendanceTable(): bool{
        try {
            $period = CarbonPeriod::create('2023-03-21', Carbon::now()->toDateString());

            // Iterate over the period
            foreach ($period as $date) {
                self::populateCacheAttendanceByDate($date->format('Y-m-d'));
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
        return true;
    }

}
