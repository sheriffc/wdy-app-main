<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class LearnerProfile {

    public function learnerDetails($learnerUuid) {
        $sql = "
            SELECT
                l.uuid,
                l.learner_id,
                p.first_name,
                p.middle_name,
                p.last_name,
                CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) full_name,
                p.nin,
                p.date_of_birth,
                p.sex_oid,
                ol_sex.item_name sex,
                p.portrait_uuid,

                s.uuid school_uuid,
                s.name school_name,
                sla.uuid admission_uuid,
                sla.start_date,
                sla.end_date,
                sla.end_reason_learner_oid,
                ol_er.item_name end_reason_name,

                sg.school_group_name,
                ol_sgl.item_name school_group_level,
                sle.academic_year,

                l.language_oid_strongest,
                ol_lang.item_name language_name,

                l.maternal_status_oid,
                ol_mat.item_name maternal_status,

                l.disability_severity_oid_vision,
                ol_dv.item_name disability_vision,
                l.disability_severity_oid_hearing,
                ol_dh.item_name disability_hearing,
                l.disability_severity_oid_mobility,
                ol_dm.item_name disability_mobility,
                l.disability_severity_oid_cognition,
                ol_dc.item_name disability_cognition,
                l.disability_severity_oid_selfcare,
                ol_ds.item_name disability_selfcare,
                l.disability_severity_oid_communication,
                ol_dcom.item_name disability_communication,
                l.disability_other_condition_oid,
                ol_doc.item_name disability_other_condition,

                pg.uuid guardian_uuid,
                CONCAT_WS(', ', pg.last_name, CONCAT_WS(' ', pg.first_name, pg.middle_name)) guardian_full_name,
                pg.nin guardian_nin,
                pg.date_of_birth guardian_date_of_birth,
                pg.sex_oid guardian_sex_oid,
                ol_gsex.item_name guardian_sex,
                pg.phone_1 guardian_phone_1,
                pg.phone_2 guardian_phone_2,
                pg.email guardian_email,
                pg.address guardian_address,
                l.guardian_relation_to_learner_oid,
                ol_rel.item_name guardian_relation,
                l.guardian_relation_to_learner_other

            FROM learner l
            INNER JOIN person p ON p.uuid = l.person_uuid

            -- most recent active admission
            LEFT JOIN school_learner_admission sla ON sla.uuid = (
                SELECT uuid FROM school_learner_admission
                WHERE learner_uuid = l.uuid AND deleted_at IS NULL
                ORDER BY created_at DESC LIMIT 1
            )
            LEFT JOIN school s ON s.uuid = sla.school_uuid

            -- active enrolment at current school for the active academic year
            LEFT JOIN school_learner_enrolment sle ON sle.uuid = (
                SELECT uuid FROM school_learner_enrolment
                WHERE learner_uuid = l.uuid
                  AND deleted_at IS NULL
                  AND academic_year = (SELECT academic_year FROM school_academic_year WHERE active = 1 LIMIT 1)
                  AND school_group_uuid IN (SELECT uuid FROM school_group WHERE school_uuid = s.uuid)
                ORDER BY created_at DESC LIMIT 1
            )
            LEFT JOIN school_group sg ON sg.uuid = sle.school_group_uuid

            LEFT JOIN option_list ol_sex   ON ol_sex.list_name   = 'sex'                        AND p.sex_oid                              = ol_sex.item_id
            LEFT JOIN option_list ol_sgl   ON ol_sgl.list_name   = 'school_group_level'          AND sg.school_group_level_oid               = ol_sgl.item_id
            LEFT JOIN option_list ol_lang  ON ol_lang.list_name  = 'language'                   AND l.language_oid_strongest                = ol_lang.item_id
            LEFT JOIN option_list ol_mat   ON ol_mat.list_name   = 'maternal_status'             AND l.maternal_status_oid                   = ol_mat.item_id
            LEFT JOIN option_list ol_er    ON ol_er.list_name    = 'end_reason_learner'          AND sla.end_reason_learner_oid              = ol_er.item_id
            LEFT JOIN option_list ol_dv    ON ol_dv.list_name    = 'disability_severity'         AND l.disability_severity_oid_vision        = ol_dv.item_id
            LEFT JOIN option_list ol_dh    ON ol_dh.list_name    = 'disability_severity'         AND l.disability_severity_oid_hearing       = ol_dh.item_id
            LEFT JOIN option_list ol_dm    ON ol_dm.list_name    = 'disability_severity'         AND l.disability_severity_oid_mobility      = ol_dm.item_id
            LEFT JOIN option_list ol_dc    ON ol_dc.list_name    = 'disability_severity'         AND l.disability_severity_oid_cognition     = ol_dc.item_id
            LEFT JOIN option_list ol_ds    ON ol_ds.list_name    = 'disability_severity'         AND l.disability_severity_oid_selfcare      = ol_ds.item_id
            LEFT JOIN option_list ol_dcom  ON ol_dcom.list_name  = 'disability_severity'         AND l.disability_severity_oid_communication = ol_dcom.item_id
            LEFT JOIN option_list ol_doc   ON ol_doc.list_name   = 'disability_other_condition'  AND l.disability_other_condition_oid        = ol_doc.item_id
            LEFT JOIN person pg            ON pg.uuid            = l.guardian_person_uuid
            LEFT JOIN option_list ol_gsex  ON ol_gsex.list_name  = 'sex'                         AND pg.sex_oid                             = ol_gsex.item_id
            LEFT JOIN option_list ol_rel   ON ol_rel.list_name   = 'guardian_relation_to_learner' AND l.guardian_relation_to_learner_oid     = ol_rel.item_id

            WHERE l.uuid = ?
        ";
        return DB::selectOne($sql, [$learnerUuid]);
    }

    public function schoolHistory($learnerUuid) {
        $sql = "
            SELECT
                s.name school_name,
                ol_sgl.item_name year_group,
                sg.school_group_name classroom,
                sle.academic_year,
                sla.start_date,
                sla.end_date,
                ol_er.item_name end_reason,
                sle.deleted_at
            FROM school_learner_enrolment sle
            JOIN school_group sg ON sg.uuid = sle.school_group_uuid
            JOIN school s ON s.uuid = sg.school_uuid
            LEFT JOIN school_learner_admission sla ON sla.uuid = (
                SELECT uuid FROM school_learner_admission
                WHERE learner_uuid = sle.learner_uuid
                  AND school_uuid = sg.school_uuid
                  AND deleted_at IS NULL
                ORDER BY created_at DESC LIMIT 1
            )
            LEFT JOIN option_list ol_sgl ON ol_sgl.list_name = 'school_group_level' AND sg.school_group_level_oid = ol_sgl.item_id
            LEFT JOIN option_list ol_er  ON ol_er.list_name  = 'end_reason_learner' AND sla.end_reason_learner_oid = ol_er.item_id
            WHERE sle.learner_uuid = ?
            ORDER BY sle.academic_year DESC, sle.updated_at DESC
        ";
        return DB::select($sql, [$learnerUuid]);
    }

    public function attendanceRecords($learnerUuid, $startDate, $endDate) {
        $sql = "
            SELECT
                pa.date,
                DATE_FORMAT(pa.date, '%W, %D %b %Y') date_formatted,
                ol_am.item_name attendance_am,
                ol_pm.item_name attendance_pm,
                pa.absent_reason_oid,
                ol_ar.item_name absent_reason,
                pa.absent_reason_other,
                s.name school_name
            FROM person_attendance pa
            INNER JOIN learner l ON l.person_uuid = pa.person_uuid
            LEFT JOIN school_learner_admission sla
                ON sla.learner_uuid = l.uuid
                AND sla.school_uuid = pa.school_uuid
            LEFT JOIN school s ON s.uuid = pa.school_uuid
            LEFT JOIN option_list ol_am ON ol_am.list_name = 'attendance_status' AND pa.attendance_am_status_oid = ol_am.item_id
            LEFT JOIN option_list ol_pm ON ol_pm.list_name = 'attendance_status' AND pa.attendance_pm_status_oid = ol_pm.item_id
            LEFT JOIN option_list ol_ar ON ol_ar.list_name = 'absent_reason' AND pa.absent_reason_oid = ol_ar.item_id
            WHERE l.uuid = ?
                AND pa.submitted = 1
                AND pa.deleted_at IS NULL
                AND pa.entity_type_oid = 'learner'
                AND pa.date BETWEEN ? AND ?
            ORDER BY pa.date DESC
            LIMIT 200
        ";
        return DB::select($sql, [$learnerUuid, $startDate, $endDate]);
    }

    public function performanceRecords($learnerUuid) {
        $sql = "
            SELECT
                lp.academic_year,
                lp.term_oid,
                lp.subject_oid,
                COALESCE(ol_subj.item_name, lp.subject_oid) subject_name,
                COALESCE(ol_subj.display_order, 9999)       subject_order,
                lp.assessment_1_score,
                lp.assessment_2_score,
                lp.max_score,
                sg.school_group_name,
                ol_sgl.item_name school_group_level
            FROM learner_performance lp
            LEFT JOIN school_group sg
                ON sg.uuid = lp.school_group_uuid
            LEFT JOIN option_list ol_sgl
                ON ol_sgl.list_name = 'school_group_level'
                AND sg.school_group_level_oid = ol_sgl.item_id
            LEFT JOIN option_list ol_subj
                ON ol_subj.list_name = 'school_subject'
                AND ol_subj.item_id = lp.subject_oid
            WHERE lp.learner_uuid = ?
                AND lp.deleted_at IS NULL
            ORDER BY
                lp.academic_year DESC,
                FIELD(lp.term_oid, 'first_term', 'second_term', 'third_term'),
                COALESCE(ol_subj.display_order, 9999),
                ol_subj.item_name
        ";
        return DB::select($sql, [$learnerUuid]);
    }

    public function attendanceSummary($learnerUuid) {
        $sql = "
            SELECT
                COUNT(*) total_days,
                SUM(CASE WHEN attendance_am_status_oid = 'present' AND attendance_pm_status_oid = 'present' THEN 1 ELSE 0 END) present_full,
                SUM(CASE WHEN attendance_am_status_oid = 'late'    OR  attendance_pm_status_oid = 'late'    THEN 1 ELSE 0 END) late,
                SUM(CASE WHEN attendance_am_status_oid != attendance_pm_status_oid
                          AND NOT (attendance_am_status_oid = 'present' AND attendance_pm_status_oid = 'present') THEN 1 ELSE 0 END) half_day,
                SUM(CASE WHEN attendance_am_status_oid = 'absent'  AND attendance_pm_status_oid = 'absent'  THEN 1 ELSE 0 END) absent
            FROM person_attendance pa
            INNER JOIN learner l ON l.person_uuid = pa.person_uuid
            WHERE l.uuid = ?
                AND pa.submitted = 1
                AND pa.deleted_at IS NULL
                AND pa.entity_type_oid = 'learner'
        ";
        return DB::selectOne($sql, [$learnerUuid]);
    }
}
