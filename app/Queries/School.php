<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class School{

    public function getMaxDateTeacherAttendance($schoolUuid){
        $sql = "
            SELECT
                MAX(date) max_attendance_date
            FROM person_attendance
            WHERE entity_type_oid = 'teacher'
                AND submitted = 1
                AND deleted_at IS NULL
                AND school_uuid = ?
            LIMIT 1
        ";
        return DB::selectOne($sql,[$schoolUuid]);
    }

    public function getDropdownDatesTeacherAttendance($schoolUuid, $inputDate){
        $sql = "
            SELECT
                date,
                DATE_FORMAT(date, '%W, %D  %b %Y') format_date
            FROM person_attendance
            WHERE entity_type_oid = 'teacher'
                AND submitted = 1
                AND deleted_at IS NULL
                AND school_uuid = ?
                AND date BETWEEN DATE_SUB(?,INTERVAL 10 DAY) AND DATE_ADD(?,INTERVAL 10 DAY)
            GROUP BY date
            ORDER BY date DESC
            LIMIT 20
        ";
        return DB::select($sql,[$schoolUuid,$inputDate,$inputDate]);
    }

    public function getSchoolDetails($schoolUuid){
        $sql = "
            SELECT
                s.uuid,
                s.name school_name,
                ol.item_name education_level,
                CASE WHEN s.school_education_level_oid IN ('jss', 'sss') THEN 'Principal' ELSE 'Head Teacher' END head_teacher_label,
                NULLIF(s.emis_id, '') emis_id,
                NULLIF(s.payroll_sid, '') payroll_sid,
                NULLIF(s.waec_id, '') waec_id,
                NULLIF(CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)), '') head_teacher_name,
                COALESCE(
                    NULLIF(do_uuid.name, ''),
                    NULLIF(do_dist.name, '')
                ) district_name
            FROM school s
            LEFT JOIN option_list ol ON ol.item_id = s.school_education_level_oid AND ol.list_name = 'school_education_level'
            LEFT JOIN teacher t ON t.school_uuid = s.uuid
                AND t.teacher_role_oid = CASE WHEN s.school_education_level_oid IN ('jss', 'sss') THEN 'principal' ELSE 'head_teacher' END
            LEFT JOIN person p ON p.uuid = t.person_uuid
            LEFT JOIN district_office do_uuid ON do_uuid.uuid = s.district_office_uuid
            LEFT JOIN district_office do_dist ON do_dist.district_id = s.district_id
            WHERE s.uuid = ?
        ";
        return DB::selectOne($sql,[$schoolUuid]);
    }

    public function getTeacherTable($schoolUuid, $date, $isDistrictOfficerOrAbove){
        $confidentialColumns = "
                null teacher_name,
                IF(t.pin is null,'Non-Payroll','Payroll') pin
            ";
        if($isDistrictOfficerOrAbove){
            $confidentialColumns = "
                CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) teacher_name,
                COALESCE(t.pin,'Non-Payroll') pin
            ";
        }
        $sql = "
            SELECT
                t.person_uuid uuid,
                ols.item_name gender,
                olr.item_name teacher_role,
                pa.attendance_status_oid,
                ola.item_name attendance_status,
                pa.biometric_method_oid,
                pa.attendance_created_at,
                IFNULL(IF(absent_reason_oid,'other_valid_reason',CONCAT_WS(': ',art.item_name,pa.absent_reason_other)),art.item_name) absent_reason,
                {$confidentialColumns}
            FROM teacher t
            LEFT JOIN person p ON p.uuid = t.person_uuid
            LEFT JOIN option_list ols ON ols.item_id = p.sex_oid AND ols.list_name = 'sex'
            LEFT JOIN option_list olr ON olr.item_id = t.teacher_role_oid AND olr.list_name = 'teacher_role'
            LEFT JOIN(
                SELECT
                    person_uuid,
                    attendance_status_oid,
                    biometric_method_oid,
                    absent_reason_oid,
                    absent_reason_other,
                    created_at attendance_created_at
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                    AND date = ?
            ) pa ON t.person_uuid = pa.person_uuid
            LEFT JOIN option_list ola ON ola.item_id = pa.attendance_status_oid AND ola.list_name = 'attendance_status'
            LEFT JOIN option_list art ON art.item_id = pa.absent_reason_oid AND art.list_name ='absent_reason_teacher'
            WHERE t.school_uuid = ?
                -- check when they started/ended at the school to only show people active on that day
                -- ideally we should be using start_date/end_date, but these fields are rarely being filled by users
                -- also these fields are problematic for preloaded payroll teachers who never even worked at the school to begin
                -- so using created_at/deleted_at for now
                AND (t.created_at < DATE_ADD(CAST(? AS DATETIME), INTERVAL 1 DAY))
                AND (t.deleted_at IS NULL OR t.deleted_at >= DATE_ADD(CAST(? AS DATETIME), INTERVAL 1 DAY))
            ORDER BY CAST(olr.item_extra AS SIGNED) DESC, -- order by seniority of their role
                teacher_name
            ";
        return DB::select($sql,[$date,$schoolUuid,$date,$date]);
    }

    public function getTeacherAttendanceBarchart($schoolUuid, $startDate, $endDate){
        $sql = "
            WITH recursive Date_Ranges AS (
                SELECT ? as date
                    UNION ALL
                    SELECT Date + interval 1 day
                    FROM Date_Ranges
                    WHERE Date < ?
            )
            SELECT
                DATE_FORMAT(dr.date, '%a, %D  %b %Y') date,
               	SUM(IFNULL(IF(attendance_breakdown.attendance_status_oid = 'present', attendance_breakdown.count,0),0)) teachers_on_time,
               	SUM(IFNULL(IF(attendance_breakdown.attendance_status_oid = 'late', attendance_breakdown.count,0),0)) teachers_late,
               	SUM(IFNULL(IF(attendance_breakdown.attendance_status_oid = 'absent', attendance_breakdown.count,0),0)) teachers_absent,
                SUM(IFNULL(s_teachers.count,0)) teachers_total
            FROM Date_Ranges dr
            LEFT JOIN (
                SELECT
                    school_uuid,
                    date,
                    attendance_status_oid,
                    COUNT(*) as count
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                    AND school_uuid = ?
                GROUP BY date, attendance_status_oid
            ) AS attendance_breakdown ON dr.date = attendance_breakdown.date
            -- TODO: the handling of created_at/deleted_at checks is very problematic in the total teachers count below
            LEFT JOIN (
                SELECT
                    school_uuid,
                    created_at,
                    COUNT(*) as count
                FROM teacher
                WHERE deleted_at IS NULL
                    AND school_uuid = ?
            ) AS s_teachers ON s_teachers.school_uuid = attendance_breakdown.school_uuid
                AND s_teachers.created_at < DATE_ADD(CAST(dr.date AS DATETIME), INTERVAL 1 DAY)
            GROUP BY dr.date
        ";

        return DB::select($sql,[$startDate,$endDate,$schoolUuid,$schoolUuid]);
    }

    public function getLearnerTable($schoolUuid, $date, $isDistrictOfficerOrAbove){
        $confidentialColumns = "
            null learner_name,
            null date_of_birth
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns = "
                CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) learner_name,
                p.date_of_birth
            ";
        }
        $sql = "
            SELECT
                l.uuid learner_uuid,
                ols.item_name gender,
                olsgl.item_name year_group,
                pa.attendance_status,
                COALESCE(ol_ar.item_name, pa.absent_reason_other) absent_reason,
                {$confidentialColumns}
            FROM school_learner_admission sla
            LEFT JOIN learner l ON l.uuid = sla.learner_uuid
            LEFT JOIN person p ON p.uuid = l.person_uuid
            LEFT JOIN option_list ols ON ols.item_id = p.sex_oid AND ols.list_name = 'sex'
            LEFT JOIN school_learner_enrolment sle
                ON sle.learner_uuid = l.uuid
                AND sle.deleted_at IS NULL
                AND sle.academic_year = (SELECT academic_year FROM school_academic_year WHERE active = 1 LIMIT 1)
                AND sle.school_group_uuid IN (SELECT uuid FROM school_group WHERE school_uuid = ?)
            LEFT JOIN school_group sg ON sg.uuid = sle.school_group_uuid
            LEFT JOIN option_list olsgl ON olsgl.item_id = sg.school_group_level_oid AND olsgl.list_name = 'school_group_level'
            LEFT JOIN(
                SELECT
                    person_uuid,
                    (CASE
                        WHEN attendance_am_status_oid = attendance_pm_status_oid THEN attendance_am_status_oid
                        WHEN attendance_am_status_oid != attendance_pm_status_oid THEN 'half_day'
                        ELSE NULL
                    END) AS attendance_status,
                    absent_reason_oid,
                    absent_reason_other
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'learner'
                    AND date = ?
            ) pa ON pa.person_uuid = l.person_uuid
            LEFT JOIN option_list ol_ar ON ol_ar.item_id = pa.absent_reason_oid AND ol_ar.list_name = 'absent_reason'
            WHERE sla.school_uuid = ?
                AND (l.learner_id IS NOT NULL AND l.learner_id != '')
                AND (sla.created_at < DATE_ADD(CAST(? AS DATETIME), INTERVAL 1 DAY))
                AND (sla.deleted_at IS NULL OR sla.deleted_at >= DATE_ADD(CAST(? AS DATETIME), INTERVAL 1 DAY))
            ORDER BY
                CAST(olsgl.item_extra AS SIGNED) DESC, -- order by oldest yeargroup first
                learner_name
        ";
        return DB::select($sql,[$schoolUuid,$date,$schoolUuid,$date,$date]);
    }

    public function getClassroomTable($schoolUuid,$date){
        $sql ="
            SELECT
                sg.school_group_name classroom_name,
                olsgl.item_name year_group,
                count(sle.uuid) learner_count
            FROM school_group sg
            LEFT JOIN option_list olsgl ON olsgl.item_id = sg.school_group_level_oid AND olsgl.list_name = 'school_group_level'
            LEFT JOIN school_learner_enrolment sle ON sle.school_group_uuid = sg.uuid
                AND sle.academic_year = (SELECT academic_year FROM school_academic_year WHERE active = 1 LIMIT 1)
                AND (sle.deleted_at IS NULL or sle.deleted_at >= DATE_ADD(CAST(? AS DATETIME), INTERVAL 1 DAY))
            WHERE sg.school_uuid = ?
                AND (sg.created_at < DATE_ADD(CAST(? AS DATETIME), INTERVAL 1 DAY))
                AND (sg.deleted_at IS NULL OR sg.deleted_at >= DATE_ADD(CAST(? AS DATETIME), INTERVAL 1 DAY))
            GROUP BY sg.uuid
            ORDER BY
                CAST(olsgl.item_extra AS SIGNED) DESC, -- order by oldest yeargroup first
                sg.school_group_name
        ";
        return DB::select($sql,[$date,$schoolUuid,$date,$date]);
    }

    public function getTeachersRemovedFromPayroll($schoolUuid){
        $sql="
            SELECT
                t.person_uuid uuid,
                pin,
                CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) teacher_name,
                DATE_FORMAT(t.deleted_at, '%a %D  %b %Y')  date_removed,
                DATE_FORMAT(t.end_date, '%a %D  %b %Y')  end_date_at_school,
                olert.item_name reason,
                t.end_reason_teacher_detail reason_detail
            FROM teacher t
            LEFT JOIN person p ON p.uuid = t.person_uuid
            LEFT JOIN option_list olert ON olert.item_id = t.end_reason_teacher_oid AND olert.list_name = 'end_reason_teacher'
            WHERE t.school_uuid = ?
                AND t.deleted_at is not null
                AND t.employment_status_oid = 'payroll'
            ORDER BY
                teacher_name
        ";
        return DB::select($sql,[$schoolUuid]);
    }

}
