<?php

namespace App\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataSyncQueriesV3
{
    //UNI DIRECTIONAL
    public static function nonPayrollTeachers($fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $pinFilter = "(pin IS NULL OR pin = '')";
        $where = "WHERE $pinFilter";
        if($fromSyncedAt && $fromPk && $installId){
            $where .= " AND ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk') )";
        }

        $sql = "
        SELECT
        uuid,
        school_sid,
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
        deleted_at,
        synced_at
        FROM teacher_payroll
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function teacherPayroll($fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $pinFilter = "(pin IS NOT NULL AND pin != '')";
        $where = "WHERE $pinFilter";
        if($fromSyncedAt && $fromPk && $installId){
            $where .= " AND ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk') )";
        }

        $sql = "
        SELECT
        uuid,
        school_sid,
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
        deleted_at,
        synced_at
        FROM teacher_payroll
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function districtOffice($fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($fromSyncedAt && $fromPk){
            $where = "WHERE ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk') )";
        }

        $sql = "
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
        deleted_at,
        synced_at
        FROM district_office
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function schoolAcademicYear($fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($fromSyncedAt && $fromPk){
            $where = "WHERE ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk' ) )";
        }

        $sql = "
        SELECT
        uuid,
        academic_year_name,
        academic_year,
        date_from,
        date_to,
        active,
        created_at,
        updated_at,
        deleted_at,
        synced_at
        FROM school_academic_year
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function optionList($fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($fromSyncedAt && $fromPk){
            $where = "WHERE ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND id > '$fromPk') )";
        }

        $sql = "
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
        updated_at,
        deleted_at,
        synced_at
        FROM option_list
        $where
        ORDER BY synced_at,id
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function optionListLink($fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($fromSyncedAt && $fromPk){
            $where = "WHERE ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND id > '$fromPk') )";
        }

        $sql = "
        SELECT
        id,
        parent_list_name,
        child_list_name,
        parent_id,
        child_id,
        created_at,
        updated_at,
        deleted_at,
        synced_at
        FROM option_list_link
        $where
        ORDER BY synced_at,id
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function geo($fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($fromSyncedAt && $fromPk){
            $where = "WHERE ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND id > '$fromPk') )";
        }

        $sql = "
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
        deleted_at,
        synced_at
        FROM geo
        $where
        ORDER BY synced_at,id
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    // Global (uni-directional) learner sync — sends ALL learners regardless of school affiliation
    public static function learnerGlobal($fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($fromSyncedAt && $fromPk) {
            $where = "WHERE ( l.synced_at > '$fromSyncedAt' OR (l.synced_at = '$fromSyncedAt' AND l.uuid > '$fromPk') )
                            AND NOT (l.synced_at > '$fromSyncedAt' AND l.synced_by_install_id IS NOT NULL AND l.synced_by_install_id = '$installId')";
        }

        $sql = "
        SELECT
        l.uuid,
        l.person_uuid,
        l.learner_id,
        l.language_oid_strongest,
        l.maternal_status_oid,
        l.maternal_status_updated_at,
        l.disability_severity_oid_vision,
        l.disability_severity_oid_hearing,
        l.disability_severity_oid_mobility,
        l.disability_severity_oid_cognition,
        l.disability_severity_oid_selfcare,
        l.disability_severity_oid_communication,
        l.disability_other_condition_oid,
        l.guardian_person_uuid,
        l.guardian_relation_to_learner_oid,
        l.guardian_relation_to_learner_other,
        l.created_at,
        l.created_by,
        l.updated_at,
        l.updated_by,
        l.deleted_at,
        l.deleted_by,
        l.synced_at
        FROM learner l
        $where
        ORDER BY l.synced_at, l.uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    //BI DIRECTIONAL
    public static function learner($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "sla.school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( l.synced_at > '$fromSyncedAt' OR (l.synced_at = '$fromSyncedAt' AND l.uuid > '$fromPk') )
                            AND NOT (l.synced_at > '$fromSyncedAt' AND l.synced_by_install_id IS NOT NULL AND l.synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
        SELECT
        l.uuid,
        l.person_uuid,
        l.learner_id,
        l.language_oid_strongest,
        l.maternal_status_oid,
        l.maternal_status_updated_at,
        l.disability_severity_oid_vision,
        l.disability_severity_oid_hearing,
        l.disability_severity_oid_mobility,
        l.disability_severity_oid_cognition,
        l.disability_severity_oid_selfcare,
        l.disability_severity_oid_communication,
        l.disability_other_condition_oid,
        l.guardian_person_uuid,
        l.guardian_relation_to_learner_oid,
        l.guardian_relation_to_learner_other,
        l.created_at,
        l.created_by,
        l.updated_at,
        l.updated_by,
        l.deleted_at,
        l.deleted_by,
        l.synced_at
        FROM learner l INNER JOIN school_learner_admission sla ON l.uuid = sla.learner_uuid
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function person($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId){
        $whereSla = '';
        $whereT = '';
        $whereUpdate = '';

        if($whereIn){
            $whereSla = "sla.school_uuid IN ($whereIn)";
            $whereT = "t.school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $whereUpdate = "WHERE ( u.synced_at > '$fromSyncedAt' OR (u.synced_at = '$fromSyncedAt' AND u.uuid > '$fromPk') )
                            AND NOT (u.synced_at > '$fromSyncedAt' AND u.synced_by_install_id IS NOT NULL AND u.synced_by_install_id = '$installId')";
        }

        if($whereSla){
            $whereSla = "WHERE $whereSla";
        }

        if($whereT){
            $whereT = "WHERE $whereT";
        }

        $sql = "
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
        u.deleted_by,
        u.synced_at
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
        UNION
        SELECT p.*
        FROM person p INNER JOIN learner l ON p.uuid = l.person_uuid
        WHERE NOT EXISTS (SELECT 1 FROM school_learner_admission sla WHERE sla.learner_uuid = l.uuid AND sla.deleted_at IS NULL)
        UNION
        SELECT p.*
        FROM person p INNER JOIN learner l ON p.uuid = l.guardian_person_uuid
        WHERE NOT EXISTS (SELECT 1 FROM school_learner_admission sla WHERE sla.learner_uuid = l.uuid AND sla.deleted_at IS NULL)
        ) u
        $whereUpdate
        ORDER BY u.synced_at,u.uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function personAttendance($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk') )
                            AND NOT (synced_at > '$fromSyncedAt' AND synced_by_install_id IS NOT NULL AND synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
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
        deleted_by,
        synced_at
        FROM person_attendance
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function school($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk') )
                            AND NOT (synced_at > '$fromSyncedAt' AND synced_by_install_id IS NOT NULL AND synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
        SELECT
        uuid,
        name,
        school_education_level_oid,
        emis_id,
        wideya_id,
        payroll_sid,
        waec_id,
        fabinc_recordid,
        district_id,
        chiefdom_id,
        section_name,
        town_name,
        address,
        classrooms_oid,
        wash_oids,
        electricity_oids,
        mno_oids,
        learning_materials_oids,
        receives_feeding,
        district_office_uuid,
        lat,
        lng,
        media_photo_uuid,
        tablet_phone_number,
        active,
        created_at,
        created_by,
        updated_at,
        updated_by,
        deleted_at,
        deleted_by,
        synced_at
        FROM school
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function schoolGroup($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk') )
                            AND NOT (synced_at > '$fromSyncedAt' AND synced_by_install_id IS NOT NULL AND synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
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
        deleted_by,
        synced_at
        FROM school_group
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function schoolLearnerAdmission($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk') )
                            AND NOT (synced_at > '$fromSyncedAt' AND synced_by_install_id IS NOT NULL AND synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
        SELECT
        uuid,
        school_uuid,
        learner_uuid,
        admission_number,
        start_date,
        end_date,
        end_reason_learner_oid,
        end_reason_learner_other,
        end_reason_learner_detail,
        created_at,
        created_by,
        updated_at,
        updated_by,
        deleted_at,
        deleted_by,
        synced_at
        FROM school_learner_admission
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function schoolLearnerEnrolment($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "sg.school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( sle.synced_at > '$fromSyncedAt' OR (sle.synced_at = '$fromSyncedAt' AND sle.uuid > '$fromPk') )
                            AND NOT (sle.synced_at > '$fromSyncedAt' AND sle.synced_by_install_id IS NOT NULL AND sle.synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
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
        sle.deleted_by,
        sle.synced_at
        FROM school_learner_enrolment sle
        INNER JOIN school_group sg ON sle.school_group_uuid = sg.uuid
        $where
        ORDER BY sle.synced_at,sle.uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function teacher($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk') )
                            AND NOT (synced_at > '$fromSyncedAt' AND synced_by_install_id IS NOT NULL AND synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
        SELECT
        uuid,
        person_uuid,
        school_uuid,
        employment_status_oid,
        teacher_role_oid,
        pin,
        nassit_number,
        tsc_licence_id,
        start_date,
        end_date,
        end_reason_teacher_oid,
        end_reason_teacher_other,
        end_reason_teacher_detail,
        created_at,
        created_by,
        updated_at,
        updated_by,
        deleted_at,
        deleted_by,
        synced_at
        FROM teacher
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function mediaPhoto($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "t.school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( mp.synced_at > '$fromSyncedAt' OR (mp.synced_at = '$fromSyncedAt' AND mp.uuid > '$fromPk') )
                            AND NOT (mp.synced_at > '$fromSyncedAt' AND mp.synced_by_install_id IS NOT NULL AND mp.synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
        SELECT
        mp.uuid,
        mp.ref_uuid,
        mp.base64_data,
        mp.display_orientation,
        mp.active,
        mp.created_at,
        mp.created_by,
        mp.updated_at,
        mp.updated_by,
        mp.deleted_at,
        mp.deleted_by,
        mp.synced_at
        FROM media_photo mp
        INNER JOIN person p ON mp.uuid = p.portrait_uuid
        INNER JOIN teacher t ON p.uuid = t.person_uuid
        $where
        ORDER BY mp.synced_at,mp.uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function personFingerprint($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "t.school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( pf.synced_at > '$fromSyncedAt' OR (pf.synced_at = '$fromSyncedAt' AND pf.uuid > '$fromPk') )
                            AND NOT (pf.synced_at > '$fromSyncedAt' AND pf.synced_by_install_id IS NOT NULL AND pf.synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
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
        pf.deleted_by,
        pf.synced_at
        FROM person_fingerprint pf
        INNER JOIN person p ON pf.person_uuid = p.uuid
        INNER JOIN teacher t ON p.uuid = t.person_uuid
        $where
        ORDER BY pf.synced_at,pf.uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function schoolFeeding($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk') )
                            AND NOT (synced_at > '$fromSyncedAt' AND synced_by_install_id IS NOT NULL AND synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
        SELECT
        uuid,
        school_uuid,
        receives_feeding,
        supply_period_oid,
        received_at,
        supplied_by_oid,
        supplied_by_other,
        qty_rice,
        qty_beans,
        qty_gari,
        qty_veg_oil,
        qty_salt,
        created_at,
        created_by,
        updated_at,
        updated_by,
        deleted_at,
        deleted_by,
        synced_at
        FROM school_feeding
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function schoolFeedingStock($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk') )
                            AND NOT (synced_at > '$fromSyncedAt' AND synced_by_install_id IS NOT NULL AND synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
        SELECT
        uuid,
        school_uuid,
        stock_month,
        qty_rice,
        qty_beans,
        qty_gari,
        qty_veg_oil,
        qty_salt,
        created_at,
        created_by,
        updated_at,
        updated_by,
        deleted_at,
        deleted_by,
        synced_at
        FROM school_feeding_stock
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function teacherTimetable($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "tt.school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( tt.synced_at > '$fromSyncedAt' OR (tt.synced_at = '$fromSyncedAt' AND tt.uuid > '$fromPk') )
                            AND NOT (tt.synced_at > '$fromSyncedAt' AND tt.synced_by_install_id IS NOT NULL AND tt.synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
        SELECT
        tt.uuid,
        tt.teacher_uuid,
        tt.school_uuid,
        tt.school_group_uuid,
        tt.school_subject_oid,
        tt.school_subject_other,
        tt.day_of_the_week_oid,
        tt.start_time,
        tt.end_time,
        tt.created_at,
        tt.created_by,
        tt.updated_at,
        tt.updated_by,
        tt.deleted_at,
        tt.deleted_by,
        tt.synced_at
        FROM teacher_timetable tt
        $where
        ORDER BY tt.synced_at,tt.uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

    public static function learnerPerformance($whereIn=false,$fromSyncedAt=false, $fromPk=false, $limit="", $installId=false){
        $where = '';
        if($whereIn){
            $where = "school_uuid IN ($whereIn)";
        }

        if($fromSyncedAt && $fromPk) {
            $where .= " AND ( synced_at > '$fromSyncedAt' OR (synced_at = '$fromSyncedAt' AND uuid > '$fromPk') )
                            AND NOT (synced_at > '$fromSyncedAt' AND synced_by_install_id IS NOT NULL AND synced_by_install_id = '$installId')";
        }

        if($where){
            $where = "WHERE $where";
        }

        $sql = "
        SELECT
        uuid,
        school_uuid,
        school_group_uuid,
        learner_uuid,
        subject_oid,
        academic_year,
        term_oid,
        assessment_1_score,
        assessment_2_score,
        max_score,
        created_at,
        created_by,
        updated_at,
        updated_by,
        deleted_at,
        deleted_by,
        synced_at
        FROM learner_performance
        $where
        ORDER BY synced_at,uuid
        $limit
        ";

        if(env('LOG_SYNC_SQL',false)) Log::channel('sync')->debug($sql);

        return DB::select($sql);
    }

}
