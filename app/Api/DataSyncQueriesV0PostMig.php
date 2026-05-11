<?php

namespace App\Api;

use Illuminate\Support\Facades\DB;

class DataSyncQueriesV0PostMig
{
    //UNI DIRECTIONAL
    public static function teacherPayroll($fromUpdatedAt=false, $fromPk=false,  $limit="", $where = ""){
        if($fromUpdatedAt && $fromPk){
//            $where = "WHERE updated_at >= '$fromUpdatedAt' AND uuid>'$fromPk'";
//            $where = "WHERE updated_at > '$fromUpdatedAt'";
            $where = "WHERE (updated_at > '$fromUpdatedAt' OR uuid > '$fromPk')";
        }

        return DB::select("
        SELECT
        uuid,
        first_name,
        middle_name,
        last_name,
        sex,
        date_of_birth,
        pin,
        nin,
        nassit_number,
        created_at,
        updated_at,
        deleted_at
        FROM teacher_payroll
        $where
        ORDER BY uuid
        $limit
        ");
    }

    public static function districtOffice($fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($fromUpdatedAt && $fromPk){
//            $where = "WHERE updated_at >= '$fromUpdatedAt' AND uuid>'$fromPk'";
//            $where = "WHERE updated_at > '$fromUpdatedAt'";
            $where = "WHERE (updated_at > '$fromUpdatedAt' OR uuid > '$fromPk')";
        }

        return DB::select("
        SELECT
        uuid,
        name,
        district_id,
        leader_id,
        lat,
        lng,
        active,
        display_order,
        created_at,
        updated_at,
        deleted_at
        FROM district_office
        $where
        ORDER BY uuid
        $limit
        ");
    }

    public static function schoolAcademicYear($fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($fromUpdatedAt && $fromPk){
//            $where = "WHERE updated_at >= '$fromUpdatedAt' AND uuid>'$fromPk'";
//            $where = "WHERE updated_at > '$fromUpdatedAt'";
            $where = "WHERE (updated_at > '$fromUpdatedAt' OR uuid > '$fromPk')";
        }

        return DB::select("
        SELECT
        uuid,
        academic_year_name,
        academic_year,
        date_from,
        date_to,
        active,
        created_at,
        updated_at
        FROM school_academic_year
        $where
        ORDER BY uuid
        $limit
        ");
    }

    public static function optionList($fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($fromUpdatedAt && $fromPk){
            $where = "WHERE (updated_at > '$fromUpdatedAt' OR id > '$fromPk')";
        }

        return DB::select("
        SELECT
        id,
        parent_id,
        list_name,
        item_name,
        item_id,
        item_extra,
        item_asc_fabinc_recordid,
        display_order,
        active,
        created_at,
        updated_at
        FROM option_list
        $where
        ORDER BY id
        $limit
        ");
    }

    public static function geo($fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($fromUpdatedAt && $fromPk){
//            $where = "WHERE updated_at >= '$fromUpdatedAt' AND uuid>'$fromPk'";
            $where = "WHERE (updated_at > '$fromUpdatedAt' OR id > '$fromPk')";
        }

        return DB::select("
        SELECT
        id,
        parent_id,
        name,
        type,
        fabinc_recordid,
        display_order,
        active,
        created_at,
        updated_at,
        deleted_at
        FROM geo
        $where
        ORDER BY id
        $limit
        ");
    }

    //BI DIRECTIONAL
    public static function learner($whereIn=false,$fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($whereIn){
            $where = "sla.school_uuid IN ($whereIn)";
        }

        if($fromUpdatedAt && $fromPk) {
//            $where .= " AND l.updated_at >= '$fromUpdatedAt' AND l.uuid>'$fromPk'";
//            $where .= " AND l.updated_at > '$fromUpdatedAt'";
            $where .= " AND (l.updated_at > '$fromUpdatedAt' OR l.uuid > '$fromPk')";
        }

        if($where){
            $where = "WHERE $where";
        }

        return DB::select("
        SELECT
        l.uuid,
        l.person_uuid,
        l.learner_id,
        l.language_oid_strongest strongest_language_oid,
        l.maternal_status_oid,
        l.maternal_status_updated_at,
        l.disability_severity_oid_vision disability_vision_oid,
        l.disability_severity_oid_hearing disability_hearing_oid,
        l.disability_severity_oid_mobility disability_mobility_oid,
        l.disability_severity_oid_cognition disability_cognition_oid,
        l.disability_severity_oid_selfcare disability_selfcare_oid,
        l.disability_severity_oid_communication disability_communication_oid,
        l.disability_other_condition_oid,
        l.guardian_person_uuid,
        l.guardian_relation_to_learner_oid,
        l.guardian_relation_to_learner_other,
        l.created_at,
        l.created_by,
        l.updated_at,
        l.updated_by,
        l.deleted_at,
        l.deleted_by
        FROM learner l INNER JOIN school_learner_admission sla ON l.uuid = sla.learner_uuid
        $where
        ORDER BY uuid
        $limit
        ");
    }

    public static function person($whereIn=false,$fromUpdatedAt=false, $fromPk=false, $limit="",$whereSla = "", $whereT = "", $whereUpdate = ""){
        if($whereIn){
            $whereSla = "sla.school_uuid IN ($whereIn)";
            $whereT = "t.school_uuid IN ($whereIn)";
        }

        if($fromUpdatedAt && $fromPk) {
//            $whereUpdate = "WHERE u.updated_at >= '$fromUpdatedAt' AND u.uuid>'$fromPk'";
//            $whereUpdate = "WHERE u.updated_at > '$fromUpdatedAt'";
            $whereUpdate = "WHERE (u.updated_at > '$fromUpdatedAt' OR u.uuid > '$fromPk')";
        }

        if($whereSla){
            $whereSla = "WHERE $whereSla";
        }

        if($whereT){
            $whereT = "WHERE $whereT";
        }

        return DB::select("
        SELECT
        u.uuid,
        u.last_name,
        u.middle_name,
        u.first_name,
        u.sex_oid,
        u.date_of_birth,
        u.nin,
        u.portrait_uuid,
        u.phone_1,
        u.phone_2,
        u.email,
        u.address,
        u.fp_lt_uuid,
        u.fp_li_uuid,
        u.fp_rt_uuid,
        u.fp_ri_uuid,
        u.created_at,
        u.created_by,
        u.updated_at,
        u.updated_by,
        u.deleted_at,
        u.deleted_by
        FROM (
        SELECT p.*
        FROM person p INNER JOIN learner l ON p.uuid = l.person_uuid INNER JOIN school_learner_admission sla ON l.uuid = sla.learner_uuid
        $whereSla
        UNION
        SELECT p.*
        FROM person p INNER JOIN learner l ON p.uuid = l.guardian_person_uuid INNER JOIN school_learner_admission sla ON l.uuid = sla.learner_uuid
        $whereSla
        UNION
        SELECT p.*
        FROM person p INNER JOIN teacher t ON p.uuid = t.person_uuid
        $whereT
        ) u
        $whereUpdate
        ORDER BY u.uuid
        $limit
        ");
    }

    public static function personAttendance($whereIn=false,$fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($whereIn){
            $where = "school_uuid IN ($whereIn)";
        }

        if($fromUpdatedAt && $fromPk) {
//            $where .= " AND updated_at >= '$fromUpdatedAt' AND uuid>'$fromPk'";
//            $where .= " AND updated_at > '$fromUpdatedAt'";
            $where .= " AND (updated_at > '$fromUpdatedAt' OR uuid > '$fromPk')";
        }

        if($where){
            $where = "WHERE $where";
        }

        return DB::select("
        SELECT
        uuid,
        date,
        person_uuid,
        entity_type_oid,
        academic_year,
        school_uuid,
        school_group_uuid,
        attendance_am_status_oid,
        attendance_pm_status_oid,
        attendance_status_oid,
        absent_reason_oid,
        absent_reason_other,
        lat,
        lng,
        biometric_method_oid,
        biometric_reference,
        submitted,
        created_at,
        created_by,
        updated_at,
        updated_by,
        deleted_at,
        deleted_by
        FROM person_attendance
        $where
        ORDER BY uuid
        $limit
        ");
    }

    public static function school($whereIn=false,$fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($whereIn){
            $where = "uuid IN ($whereIn)";
        }

        if($fromUpdatedAt && $fromPk) {
//            $where .= " AND updated_at >= '$fromUpdatedAt' AND uuid>'$fromPk'";
//            $where .= " AND updated_at > '$fromUpdatedAt'";
            $where .= " AND (updated_at > '$fromUpdatedAt' OR uuid>'$fromPk')";
        }

        if($where){
            $where = "WHERE $where";
        }

        return DB::select("
        SELECT
        uuid,
        name,
        school_education_level_oid education_level_oid,
        emis_id,
        wideya_id,
        payroll_sid,
        fabinc_recordid,
        district_id,
        chiefdom_id,
        section_name,
        town_name,
        address,
        district_office_uuid,
        lat,
        lng,
        media_photo_uuid,
        active,
        created_at,
        created_by,
        updated_at,
        updated_by,
        deleted_at,
        deleted_by
        FROM school
        $where
        ORDER BY uuid
        $limit
        ");
    }

    public static function schoolGroup($whereIn=false,$fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($whereIn){
            $where = "school_uuid IN ($whereIn)";
        }

        if($fromUpdatedAt && $fromPk) {
//            $where .= " AND updated_at >= '$fromUpdatedAt' AND uuid>'$fromPk'";
//            $where .= " AND updated_at > '$fromUpdatedAt'";
            $where .= " AND (updated_at > '$fromUpdatedAt' OR uuid > '$fromPk')";
        }

        if($where){
            $where = "WHERE $where";
        }

        return DB::select("
        SELECT
        uuid,
        school_uuid,
        academic_year,
        teacher_uuid,
        school_group_name,
        school_group_level_oid,
        active,
        created_at,
        created_by,
        updated_at,
        updated_by,
        deleted_at,
        deleted_by
        FROM school_group
        $where
        ORDER BY uuid
        $limit
        ");
    }

    public static function schoolLearnerAdmission($whereIn=false,$fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($whereIn){
            $where = "school_uuid IN ($whereIn)";
        }

        if($fromUpdatedAt && $fromPk) {
//            $where .= " AND updated_at >= '$fromUpdatedAt' AND uuid>'$fromPk'";
//            $where .= " AND updated_at > '$fromUpdatedAt'";
            $where .= " AND (updated_at > '$fromUpdatedAt' OR uuid > '$fromPk')";
        }

        if($where){
            $where = "WHERE $where";
        }

        return DB::select("
        SELECT
        uuid,
        school_uuid,
        learner_uuid,
        admission_number,
        start_date,
        end_date,
        end_reason_learner_oid end_reason_oid,
        end_reason_learner_other end_reason_other,
        end_reason_learner_detail end_reason_detail,
        created_at,
        created_by,
        updated_at,
        updated_by,
        deleted_at,
        deleted_by
        FROM school_learner_admission
        $where
        ORDER BY uuid
        $limit
        ");
    }

    public static function schoolLearnerEnrolment($whereIn=false,$fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($whereIn){
            $where = "sg.school_uuid IN ($whereIn)";
        }

        if($fromUpdatedAt && $fromPk) {
//            $where .= " AND sle.updated_at >= '$fromUpdatedAt' AND sle.uuid>'$fromPk'";
//            $where .= " AND sle.updated_at > '$fromUpdatedAt'";
            $where .= " AND (sle.updated_at > '$fromUpdatedAt' OR sle.uuid > '$fromPk')";
        }

        if($where){
            $where = "WHERE $where";
        }

        return DB::select("
        SELECT
        sle.uuid,
        sle.academic_year,
        sle.learner_uuid,
        sle.school_group_uuid,
        sle.created_at,
        sle.created_by,
        sle.updated_at,
        sle.updated_by,
        sle.deleted_at,
        sle.deleted_by
        FROM school_learner_enrolment sle
        INNER JOIN school_group sg ON sle.school_group_uuid = sg.uuid
        $where
        ORDER BY sle.uuid
        $limit
        ");
    }

    public static function teacher($whereIn=false,$fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($whereIn){
            $where = "school_uuid IN ($whereIn)";
        }

        if($fromUpdatedAt && $fromPk) {
//            $where .= " AND updated_at >= '$fromUpdatedAt' AND uuid>'$fromPk'";
//            $where .= " AND updated_at > '$fromUpdatedAt'";
            $where .= " AND (updated_at > '$fromUpdatedAt' OR uuid > '$fromPk')";
        }

        if($where){
            $where = "WHERE $where";
        }

        return DB::select("
        SELECT
        uuid,
        person_uuid,
        school_uuid,
        employment_status_oid,
        teacher_role_oid employment_role_oid,
        pin,
        nassit_number,
        tsc_licence_id,
        start_date,
        end_date,
        end_reason_teacher_oid end_reason_oid,
        end_reason_teacher_other end_reason_other,
        end_reason_teacher_detail end_reason_detail,
        created_at,
        created_by,
        updated_at,
        updated_by,
        deleted_at,
        deleted_by
        FROM teacher
        $where
        ORDER BY uuid
        $limit
        ");
    }

    public static function mediaPhoto($whereIn=false,$fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($whereIn){
            $where = "t.school_uuid IN ($whereIn)";
        }

        if($fromUpdatedAt && $fromPk) {
//            $where .= " AND mp.updated_at >= '$fromUpdatedAt' AND mp.uuid>'$fromPk'";
//            $where .= " AND mp.updated_at > '$fromUpdatedAt'";
            $where .= " AND (mp.updated_at > '$fromUpdatedAt' OR mp.uuid > '$fromPk')";
        }

        if($where){
            $where = "WHERE $where";
        }

        return DB::select("
        SELECT
        mp.uuid,
        mp.ref_uuid,
        mp.base64_dat binary_data,
        mp.active,
        mp.created_at,
        mp.created_by,
        mp.updated_at,
        mp.updated_by,
        mp.deleted_at,
        mp.deleted_by
        FROM media_photo mp
        INNER JOIN person p ON mp.uuid = p.portrait_uuid
        INNER JOIN teacher t ON p.uuid = t.person_uuid
        $where
        ORDER BY mp.uuid
        $limit
        ");
    }

    public static function personFingerprint($whereIn=false,$fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($whereIn){
            $where = "t.school_uuid IN ($whereIn)";
        }

        if($fromUpdatedAt && $fromPk) {
//            $where .= " AND pf.updated_at >= '$fromUpdatedAt' AND pf.uuid>'$fromPk'";
//            $where .= " AND pf.updated_at > '$fromUpdatedAt'";
            $where .= " AND (pf.updated_at > '$fromUpdatedAt' OR pf.uuid > '$fromPk')";
        }

        if($where){
            $where = "WHERE $where";
        }

        return DB::select("
        SELECT
        pf.uuid,
        pf.person_uuid,
        pf.finger_position_oid,
        pf.fp_a_cbor,
        pf.fp_a_nfiq,
        pf.fp_b_cbor,
        pf.fp_b_nfiq,
        pf.created_at,
        pf.created_by,
        pf.updated_at,
        pf.updated_by,
        pf.deleted_at,
        pf.deleted_by
        FROM person_fingerprint pf
        INNER JOIN person p ON pf.person_uuid = p.uuid
        INNER JOIN teacher t ON p.uuid = t.person_uuid
        $where
        ORDER BY pf.uuid
        $limit
        ");
    }

    public static function teacherTimetable($whereIn=false,$fromUpdatedAt=false, $fromPk=false, $limit="", $where = ""){
        if($whereIn){
            $where = "tt.school_uuid IN ($whereIn)";
        }

        if($fromUpdatedAt && $fromPk) {
//            $where .= " AND tt.updated_at > '$fromUpdatedAt'";
            $where .= " AND (tt.updated_at > '$fromUpdatedAt' OR tt.uuid > '$fromPk')";
        }

        if($where){
            $where = "WHERE $where";
        }

        return DB::select("
        SELECT
        tt.uuid,
        tt.teacher_uuid,
        tt.school_uuid,
        tt.school_group_uuid,
        tt.school_subject_oid subject_oid,
        tt.school_subject_other subject_other,
        tt.day_of_the_week_oid day_name,
        tt.start_time,
        tt.end_time,
        tt.created_at,
        tt.created_by,
        tt.updated_at,
        tt.updated_by,
        tt.deleted_at,
        tt.deleted_by
        FROM teacher_timetable tt
        $where
        ORDER BY tt.uuid
        $limit
        ");
    }

}
