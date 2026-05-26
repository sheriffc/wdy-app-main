<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class LearnerReports{
    public function learnerDisabilityTable($districtId,$isDistrictOfficerOrAbove){

        $whereClause = "";
        if($districtId != null){
            $whereClause.=" AND s.district_id = {$districtId} ";
        }

        $confidentialColumns = "
            null learner_name,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns=" 
                CONCAT_WS(', ',p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) learner_name,
            ";
        }


        $sql="
            SELECT
                p.uuid,
                {$confidentialColumns}
                s.uuid school_uuid,
                s.name school_name,
                dsv.item_name vision,
                dsh.item_name hearing,
                dsm.item_name mobility,
                dsc.item_name cognition,
                dss.item_name selfcare,
                dscom.item_name communication
            FROM learner l
            LEFT JOIN person p ON p.uuid = l.person_uuid
            LEFT JOIN school_learner_admission sla ON sla.learner_uuid = l.uuid AND sla.deleted_at IS NULL
            LEFT JOIN school s ON s.uuid = sla.school_uuid
            INNER JOIN school_learner_enrolment sle ON sle.learner_uuid = l.uuid
                AND sle.deleted_at IS NULL
                AND sle.academic_year = (SELECT academic_year FROM school_academic_year WHERE active = 1 LIMIT 1)
            LEFT JOIN option_list dsv ON dsv.list_name = 'disability_severity' AND dsv.item_id = l.disability_severity_oid_vision
            LEFT JOIN option_list dsh ON dsh.list_name = 'disability_severity' AND dsh.item_id = l.disability_severity_oid_hearing
            LEFT JOIN option_list dsm ON dsm.list_name = 'disability_severity' AND dsm.item_id = l.disability_severity_oid_mobility
            LEFT JOIN option_list dsc ON dsc.list_name = 'disability_severity' AND dsc.item_id = l.disability_severity_oid_cognition
            LEFT JOIN option_list dss ON dss.list_name = 'disability_severity' AND dss.item_id = l.disability_severity_oid_selfcare
            LEFT JOIN option_list dscom ON dscom.list_name = 'disability_severity' AND dscom.item_id = l.disability_severity_oid_communication
            WHERE l.deleted_at IS NULL
                AND (
                    (l.disability_severity_oid_vision IS NOT NULL AND l.disability_severity_oid_vision != '0_no_difficulty')
                    OR (l.disability_severity_oid_hearing IS NOT NULL AND l.disability_severity_oid_hearing != '0_no_difficulty')
                    OR (l.disability_severity_oid_mobility IS NOT NULL AND l.disability_severity_oid_mobility != '0_no_difficulty')
                    OR (l.disability_severity_oid_cognition IS NOT NULL AND l.disability_severity_oid_cognition != '0_no_difficulty')
                    OR (l.disability_severity_oid_selfcare IS NOT NULL AND l.disability_severity_oid_selfcare != '0_no_difficulty')
                    OR (l.disability_severity_oid_communication IS NOT NULL AND l.disability_severity_oid_communication != '0_no_difficulty')
                )
                {$whereClause}
        ";
        return DB::select($sql);
    }

    public function learnersAtRiskTable($districtId, $isDistrictOfficerOrAbove, $month = null, $year = null){

        $whereClause = "";
        if($districtId != null){
            $whereClause.=" WHERE s.district_id = {$districtId} ";
        }

        $monthFilter = "";
        if($month !== null && $year !== null){
            $monthFilter = " AND MONTH(pa.date) = " . intval($month) . " AND YEAR(pa.date) = " . intval($year);
        }

        $confidentialColumns = "
            null learner_name,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns="
                CONCAT_WS(', ',p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) learner_name,
            ";
        }

        $sql="
            SELECT
                p.uuid,
                {$confidentialColumns}
                s.uuid school_uuid,
                s.name school_name,
                COALESCE(learners_absent.absent_days, 0) absent_days,
                COALESCE(ROUND((
                learners_absent.absent_days/(SELECT COUNT(DISTINCT date) AS total_count
                    FROM person_attendance
                    WHERE entity_type_oid = 'learner'
                    AND submitted = 1
                    AND deleted_at IS NULL
                    AND date BETWEEN (SELECT date_from FROM school_academic_year WHERE active = 1 LIMIT 1)
                                 AND (SELECT date_to   FROM school_academic_year WHERE active = 1 LIMIT 1)
                    {$monthFilter}
                ))*100),0) percentage
            FROM learner l
            LEFT JOIN person p ON p.uuid = l.person_uuid
            LEFT JOIN school_learner_admission sla ON sla.learner_uuid = l.uuid AND sla.deleted_at IS NULL
            LEFT JOIN school s ON s.uuid = sla.school_uuid
            LEFT JOIN(
                SELECT
                    pa.person_uuid,
                    SUM(
                    CASE WHEN pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent'
                        AND (pa.absent_reason_oid IS NULL OR COALESCE(ol_ar.item_extra, '') != 'valid')
                        THEN 1 ELSE 0 END
                    ) AS absent_days
                FROM person_attendance pa
                LEFT JOIN option_list ol_ar ON ol_ar.list_name = 'absent_reason' AND ol_ar.item_id = pa.absent_reason_oid
                WHERE pa.entity_type_oid = 'learner'
                    AND pa.submitted = 1
                    AND pa.deleted_at IS NULL
                    AND pa.date BETWEEN (SELECT date_from FROM school_academic_year WHERE active = 1 LIMIT 1)
                                    AND (SELECT date_to   FROM school_academic_year WHERE active = 1 LIMIT 1)
                    {$monthFilter}
                GROUP BY pa.person_uuid
            ) AS learners_absent ON learners_absent.person_uuid = l.person_uuid
            {$whereClause}
        ";
        return DB::select($sql);
    }

    public function getAtRiskLearnersChart($districtId, $isDistrictOfficerOrAbove, $month = null, $year = null){
        $collection = collect($this->learnersAtRiskTable($districtId, $isDistrictOfficerOrAbove, $month, $year));

        $serverlyAbsent = $collection->where("percentage",">=",50)->flatten();
        $persistentAbsent = $collection->whereBetween("percentage",[10,49])->flatten();

        return [
            "severlyAbsentTable"=> $serverlyAbsent,
            "persistentAbsentTable"=>$persistentAbsent
        ];
    }

    public function duplicateEnrollmentTable($districtId, $isDistrictOfficerOrAbove){

        $whereClause = "";
        if($districtId != null){
            $whereClause .= " AND s.district_id = {$districtId} ";
        }

        $confidentialColumns = "null learner_name,";
        if($isDistrictOfficerOrAbove){
            $confidentialColumns = "CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) learner_name,";
        }

        $sql = "
            SELECT
                l.uuid learner_uuid,
                {$confidentialColumns}
                l.learner_id,
                s.uuid school_uuid,
                s.name school_name,
                sla.start_date,
                COALESCE(sle.academic_year, '—') academic_year,
                olsgl.item_name year_group,
                sg.school_group_name classroom
            FROM learner l
            JOIN person p ON p.uuid = l.person_uuid
            JOIN school_learner_admission sla ON sla.learner_uuid = l.uuid
                AND sla.deleted_at IS NULL
            JOIN school s ON s.uuid = sla.school_uuid
            LEFT JOIN school_learner_enrolment sle ON sle.learner_uuid = l.uuid
                AND sle.deleted_at IS NULL
                AND sle.academic_year = (SELECT academic_year FROM school_academic_year WHERE active = 1 LIMIT 1)
                AND sle.school_group_uuid IN (
                    SELECT uuid FROM school_group WHERE school_uuid = sla.school_uuid AND deleted_at IS NULL
                )
            LEFT JOIN school_group sg ON sg.uuid = sle.school_group_uuid AND sg.deleted_at IS NULL
            LEFT JOIN option_list olsgl ON olsgl.list_name = 'school_group_level'
                AND olsgl.item_id = sg.school_group_level_oid
            WHERE l.deleted_at IS NULL
                AND l.uuid IN (
                    SELECT learner_uuid
                    FROM school_learner_admission
                    WHERE deleted_at IS NULL
                    GROUP BY learner_uuid
                    HAVING COUNT(*) > 1
                )
                {$whereClause}
            ORDER BY l.learner_id, s.name
        ";
        return DB::select($sql);
    }

    public function unassignedLearnersTable($districtId,$isDistrictOfficerOrAbove){

        $whereClause = "";
        if($districtId != null){
            $whereClause.=" AND s.district_id = {$districtId} ";
        }

        $confidentialColumns = "
            null learner_name,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns=" 
                CONCAT_WS(', ',p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) learner_name,
            ";
        }

        $sql="
            SELECT
                l.uuid,
                {$confidentialColumns}
                s.uuid school_uuid,
                s.name school_name,
                CONCAT_WS(', ', ol_sgl_prev.item_name, sg_prev.school_group_name) prev_class,
                sle_prev.academic_year prev_academic_year
            FROM learner l
            INNER JOIN school_learner_admission sla ON sla.learner_uuid = l.uuid AND sla.deleted_at IS NULL
            INNER JOIN school s ON s.uuid = sla.school_uuid
            LEFT JOIN person p ON p.uuid = l.person_uuid

            -- most recent enrolment that had a class assigned (for previous class display)
            LEFT JOIN school_learner_enrolment sle_prev ON sle_prev.uuid = (
                SELECT uuid FROM school_learner_enrolment
                WHERE learner_uuid = l.uuid
                  AND school_group_uuid IS NOT NULL
                  AND school_group_uuid != ''
                ORDER BY updated_at DESC LIMIT 1
            )
            LEFT JOIN school_group sg_prev ON sg_prev.uuid = sle_prev.school_group_uuid
            LEFT JOIN option_list ol_sgl_prev ON ol_sgl_prev.list_name = 'school_group_level'
                AND sg_prev.school_group_level_oid = ol_sgl_prev.item_id

            WHERE l.deleted_at IS NULL
                AND NOT EXISTS (
                    SELECT 1 FROM school_learner_enrolment sle_cur
                    WHERE sle_cur.learner_uuid = l.uuid
                      AND sle_cur.deleted_at IS NULL
                      AND sle_cur.school_group_uuid IS NOT NULL
                      AND sle_cur.school_group_uuid != ''
                      AND sle_cur.academic_year = (SELECT academic_year FROM school_academic_year WHERE active = 1 LIMIT 1)
                )
                {$whereClause}
        ";
        return DB::select($sql);
    }
}