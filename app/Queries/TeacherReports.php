<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class TeacherReports{

    public function teachersEligbleForSanctionsTable($districtId,$chiefdomId,$month,$isDistrictOfficerOrAbove){
        $whereClause = "";
        if($districtId != null){
            $whereClause.=" AND district_id = {$districtId} ";
        }
        if($chiefdomId != null){
            $whereClause.=" AND chiefdom_id = {$chiefdomId} ";
        }
        if($month != null){
            $whereClause.=" AND date = '{$month}' ";
        }

        $confidentialColumns = "
            null teacher_name,
            null pin,
            null school_leader_name,
            null school_leader_phone_number,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns=" 
                CONCAT_WS(', ', a.last_name, CONCAT_WS('', a.first_name, a.middle_name)) teacher_name,
                a.pin, 
                max(a.school_leader_name) `school_leader_name`,
                max(a.phone_1) `school_leader_phone_number`,
            ";
        }


        $sql = "
            SELECT 
            * 
            FROM(SELECT	
                a.uuid,
                {$confidentialColumns}
                a.school_uuid,
                a.school_name,
                a.district_id,
                a.chiefdom_id,
                date,
                COUNT(*) `number_unauthorised_absences` 
            FROM (SELECT 
                    p.uuid,
                    DATE_FORMAT(pa.date, '%Y-%m') date, 
                    t.pin, 
                    p.first_name, 
                    p.middle_name, 
                    p.last_name,
                    s.uuid school_uuid,
                    s.name school_name, 
                    s.district_id,
                    s.chiefdom_id,
                    ht.phone_1, 
                    ht.school_leader_name
                FROM person_attendance pa
                LEFT JOIN person p on pa.person_uuid = p.uuid
                LEFT JOIN teacher t on pa.person_uuid = t.person_uuid
                LEFT JOIN school s on t.school_uuid = s.uuid
                LEFT JOIN (SELECT 
                                CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) school_leader_name, 
                                t.school_uuid, p.phone_1 FROM teacher t 
                                LEFT JOIN person p ON t.person_uuid = p.uuid
                                WHERE t.teacher_role_oid = 'head_teacher' )ht ON s.uuid = ht.school_uuid
                WHERE t.pin IS NOT NULL
                    AND (pa.absent_reason_oid = 'no_valid_reason_absent' OR pa.absent_reason_oid = 'no_valid_reason_early_departure')
                    ) a 
            
            GROUP BY a.pin, a.first_name, a.middle_name, a.last_name, a.school_name) b
            WHERE b.`number_unauthorised_absences` >= 6
                {$whereClause}
            ORDER BY `number_unauthorised_absences` DESC
        ";

        return DB::select($sql);
    }

    public function unauthorisedTeacherTransfersTable($districtId,$chiefdomId,$isDistrictOfficerOrAbove){
        $whereClause = "";
        if($districtId != null){
            $whereClause.=" AND district_id = {$districtId} ";
        }
        if($chiefdomId != null){
            $whereClause.=" AND chiefdom_id = {$chiefdomId} ";
        }

        $confidentialColumns = "
            null teacher_name,
            null pin,
            null phone_number,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns=" 
                max(sa.teacher_name) `teacher_name`,
                sa.pin `pin`, 
                max(sa.phone_1) `phone_number`,
            ";
        }

        $sql ="
        SELECT * FROM
            (SELECT 
                    sa.person_uuid uuid,
                    {$confidentialColumns}
                    CASE  
                        # If teacher has been not been deleted before (i.e. no end reason listed), we know they were manually added to the current school
                        WHEN max(sa.end_reason) IS NULL THEN 'Unauthorised Transfer to Current School' 
                        
                        # Otherwise, the teacher removed because they transfered to another school and the reason for removal will be displayed
                        ELSE CONCAT('Reason for Transfer: ', GROUP_CONCAT(sa.end_reason_teacher_detail SEPARATOR ', ')) END
                        AS `transfer_notes`,
                        
                    # Using GROUP_CONCAT in case they are added to/removed from multiple schools 
                    max(sa.added_to_sch_uuid) current_school_uuid,
                    CASE 
                            WHEN GROUP_CONCAT(sa.added_to_sch SEPARATOR ', ') IS NULL THEN 'Not Assigned'
                            ELSE GROUP_CONCAT(sa.added_to_sch SEPARATOR ', ')
                            END `current_school_assignment`, 
                    CASE
                        WHEN GROUP_CONCAT(sa.added_to_sch SEPARATOR ', ') IS NOT NULL AND GROUP_CONCAT(sa.sid_added_to_sch SEPARATOR ', ') IS NULL THEN 'Not available'
                        WHEN GROUP_CONCAT(sa.added_to_sch SEPARATOR ', ') IS NULL AND GROUP_CONCAT(sa.sid_added_to_sch SEPARATOR ', ') IS NULL THEN NULL
                        ELSE GROUP_CONCAT(sa.sid_added_to_sch SEPARATOR ', ') END `current_sid`, 
                    max(sa.removed_from_sch_uuid) payroll_school_uuid,
                    CASE
                        WHEN GROUP_CONCAT(DISTINCT sa.removed_from_sch SEPARATOR ', ') IS NULL THEN 'Non-Participating School'
                        ELSE GROUP_CONCAT(DISTINCT sa.removed_from_sch SEPARATOR ', ') END `payroll_school_assignment` ,
                    max(sa.payroll_sid) as `payroll_sid`
                   
            FROM
                # subquery: get all teacher school assignments that involve a transfer (e.g. removing a teacher, adding a teacher to school)
                (SELECT 
                
                    * FROM
            
                (
                SELECT 
                        t.person_uuid,
                        CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) teacher_name,
                        t.pin, 
                        s.name school_name,
                        s.district_id,
                        s.chiefdom_id,
                        er.item_name end_reason, 
                        t.end_reason_teacher_oid, 
                        t.end_reason_teacher_detail, 
                        t.created_by, 
                        t.created_at,
                        t.deleted_by,
                        IF(t.end_reason_teacher_oid IS NOT NULL AND t.created_by = -1, s.name, NULL) removed_from_sch,
                        IF(t.end_reason_teacher_oid IS NOT NULL AND t.created_by = -1, s.uuid, NULL) removed_from_sch_uuid,
                        IF(t.end_reason_teacher_oid IS NULL, s.name, NULL) added_to_sch,
                        IF(t.end_reason_teacher_oid IS NULL, s.uuid, NULL) added_to_sch_uuid,
                        IF(t.end_reason_teacher_oid IS NULL, s.payroll_sid, NULL) sid_added_to_sch,
                        IF(t.end_reason_teacher_oid IS NULL, 1, 0) active_schools,
                        tp.school_sid as payroll_sid,
                        p.phone_1,
                        CASE 
                        WHEN t.created_by = -1 AND COUNT(*) OVER (PARTITION BY t.pin) > 1 THEN 1
                        ELSE 0 END payroll_double_assigned
            
                FROM teacher t
                LEFT JOIN person p ON t.person_uuid = p.uuid
                LEFT JOIN school s ON t.school_uuid = s.uuid
                LEFT JOIN option_list er ON t.end_reason_teacher_oid = er.item_id AND list_name = 'end_reason_teacher' 
                LEFT JOIN teacher_payroll tp ON t.pin = tp.pin
                LEFT JOIN district_office d ON s.district_id = d.district_id
                
                ) sub
            
                WHERE sub.pin IS NOT NULL 
                    {$whereClause}                    
                    AND (sub.end_reason_teacher_oid = 'transfer_partner_school'     
                            OR sub.end_reason_teacher_oid = 'transfer_other_school'
                            OR (NOT sub.created_by = -1 AND sub.deleted_by IS NULL))
                    OR sub.payroll_double_assigned = 1
                    
                    ) AS sa                                                            
                
            GROUP BY sa.pin
            ) sub2
            
            WHERE (NOT sub2.`current_sid` = sub2.`payroll_sid`) OR sub2.`current_sid` IS NULL
        ";
        return DB::select($sql);
    }

    public function removableTeachersTable($districtId,$chiefdomId,$isDistrictOfficerOrAbove){
        $whereClause = "";
        if($districtId != null){
            $whereClause.=" AND s.district_id = {$districtId} ";
        }
        if($chiefdomId != null){
            $whereClause.=" AND s.chiefdom_id = {$chiefdomId} ";
        }

        $confidentialColumns = "
            null teacher_name,
            null pin,
            null removed_by,
            null school_leader_phone,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns=" 
                CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) `teacher_name`, 
                t.pin `pin`, 	
                CONCAT_WS(', ', ht.last_name, CONCAT_WS(' ', ht.first_name, ht.middle_name)) `removed_by`,
                ht.phone_1 as `school_leader_phone`,   
            ";
        }


        $sql="
            SELECT 
                t.person_uuid uuid,
                {$confidentialColumns}                                                                                 
                s.uuid school_uuid,																
                s.name `school_removed_from`,	
                er.item_name `end_reason`, 	                                                    
                t.end_reason_teacher_detail `comments`,
                DATE_FORMAT(t.end_date, '%a %D  %b %Y') `date_left_school`
                    
            FROM teacher t
            LEFT JOIN person p ON t.person_uuid = p.uuid
            LEFT JOIN school s ON t.school_uuid = s.uuid
            LEFT JOIN option_list er ON t.end_reason_teacher_oid = er.item_id AND er.list_name = 'end_reason_teacher'
            LEFT JOIN teacher_payroll pr ON t.pin = pr.pin
            LEFT JOIN district_office d ON s.district_id = d.district_id
            LEFT JOIN (
                Select t.school_uuid, t.person_uuid, p.phone_1, p.phone_2, p.first_name, p.middle_name, p.last_name, t.end_date
                FROM teacher t 
                LEFT JOIN person p ON t.person_uuid = p.uuid
                WHERE t.teacher_role_oid = 'head_teacher' 
                    ) ht ON s.uuid = ht.school_uuid
            
            WHERE t.pin IS NOT NULL AND s.active = 1 
                AND pr.pin IS NOT NULL # filter to the teachers that are still on the payroll
                {$whereClause}   
                AND (end_reason_teacher_oid = 'death'
                OR end_reason_teacher_oid = 'retirement'
                OR end_reason_teacher_oid = 'changed_profession' )
        ";
        return DB::select($sql);
    }

    public function teacherRequiringInvestigationTable($districtId,$chiefdomId,$isDistrictOfficerOrAbove){
        $whereClause = "";
        if($districtId != null){
            $whereClause.=" AND s.district_id = {$districtId} ";
        }
        if($chiefdomId != null){
            $whereClause.=" AND s.chiefdom_id = {$chiefdomId} ";
        }

        $confidentialColumns = "
            null teacher_name,
            null pin,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns=" 
                CONCAT_WS(', ', p.last_name, CONCAT_WS(' ',p.first_name, p.middle_name)) `teacher_name`,
                t.pin `pin`, 
            ";
        }

        $sql="
        SELECT 
            t.person_uuid uuid,
            {$confidentialColumns}
            s.uuid school_uuid,
            s.name `school_removed_from`, 
            er.item_name `reason_for_removal`, 
            t.end_reason_teacher_detail `comments`
        FROM teacher t
        LEFT JOIN person p ON t.person_uuid = p.uuid
        LEFT JOIN school s ON t.school_uuid = s.uuid
        LEFT JOIN option_list er ON t.end_reason_teacher_oid = er.item_id AND list_name = 'end_reason_teacher' 
        LEFT JOIN teacher_payroll pr ON t.pin = pr.pin
        LEFT JOIN district_office d ON s.district_id = d.district_id
        WHERE pr.pin IS NOT NULL # remove teachers that are not on the payroll (no need for action)
            AND t.created_by = -1    # only filter to payroll teachers assigned by the system
            {$whereClause}   # district filter
            AND (end_reason_teacher_oid = 'dont_know_person' 
            OR end_reason_teacher_oid = 'other' 
            OR end_reason_teacher_oid = 'dont_know_what_doing')
        ";
        return DB::select($sql);
    }

    public function activeTeachersTable($districtId,$chiefdomId,$isDistrictOfficerOrAbove){
        $whereClause = "";
        if($districtId != null){
            $whereClause.=" AND s.district_id = {$districtId} ";
        }
        if($chiefdomId != null){
            $whereClause.=" AND s.chiefdom_id = {$chiefdomId} ";
        }

        $confidentialColumns = "
            null teacher_name,
            null pin,
            null teacher_phone_number,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns=" 
                CONCAT_WS(', ', sub.last_name, CONCAT_WS(' ', sub.first_name, sub.middle_name)) `teacher_name`, 
                sub.pin `pin`, 
                CASE 
                    WHEN sub.phone_1 IS NULL THEN 'Missing'
                    ELSE sub.phone_1 END `teacher_phone_number`,
            ";
        }

        $sql =" 
        SELECT 
            uuid,
            {$confidentialColumns}
            sub.employment_status, 
            school_uuid,
            coalesce(teacher_role_oid,'missing') teacher_role_oid ,
            gender,
            CASE 
                WHEN sub.item_name IS NULL THEN 'Missing'
                ELSE sub.item_name END `teacher_role`,
            IF(sub.active_assignments > 1 and sub.active_assignments < 10, 'Multiple Schools', sub.school_name )`current_school`, 
            sub.district `current_district`, 
            sub.chiefdom `current_chiefdom`
            
        FROM
        
        (SELECT 
            p.uuid,
            p.first_name,
            p.middle_name, 
            p.last_name, 
            t.pin, 
            t.teacher_role_oid,
            ole.item_name employment_status, 
            ols.item_name gender,
            tr.item_name,
            s.uuid school_uuid,
            s.name school_name, 
            sl.iddistrict as district, 
            sl.idchiefdom AS chiefdom, 
            p.phone_1,
            row_number() OVER (PARTITION BY pin ORDER BY t.deleted_at, t.created_at DESC) as row_num,
            sum(IF(t.deleted_by IS NULL, 1, 0)) OVER(PARTITION BY pin) active_assignments
            
        FROM teacher t
        LEFT JOIN person p ON t.person_uuid = p.uuid
        LEFT JOIN school s ON t.school_uuid = s.uuid
        LEFT JOIN cga_300sch_list_230319 sl ON s.emis_id = sl.idemis_code
        LEFT JOIN option_list tr ON tr.list_name = 'teacher_role' and t.teacher_role_oid = tr.item_id 
        LEFT JOIN option_list ole ON ole.item_id = t.employment_status_oid AND ole.list_name = 'employment_status'
        LEFT JOIN option_list ols ON ols.item_id = p.sex_oid AND ols.list_name = 'sex'
        WHERE t.deleted_by IS NULL
            {$whereClause}
        ORDER BY pin, row_num) sub
        WHERE sub.row_num = 1 OR pin IS NULL
        
        ORDER BY active_assignments DESC;
    
        ";
        return DB::select($sql);
    }
}