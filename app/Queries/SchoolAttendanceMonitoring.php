<?php

namespace App\Queries;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SchoolAttendanceMonitoring{

    public function getSchoolAttendanceMonitoring($startDate,$endDate,$dateRange,$districtId,$isDistrictOfficerOrAbove){

        $whereClause = "";
        if($districtId != null){
            $whereClause=" AND s.district_id = {$districtId} ";
        }

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

        $selectDisplayDate = "";
        $colNumber = 1;
        foreach ($dateRange as $dr) {
           $selectDisplayDate.=" CASE 
                                    WHEN t_attendance.date = '{$dr}' AND l_attendance.date = '{$dr}' THEN 'complete'
                                    WHEN t_attendance.date IS NULL AND l_attendance.date = '{$dr}' THEN 'missing_teacher'
                                    WHEN t_attendance.date = '{$dr}' AND l_attendance.date IS NULL THEN 'missing_learner'
                                    ELSE 'missing_both' END AS 'day_{$colNumber}', ";
           $colNumber ++;
        }
        $sql="
            SELECT
                s.uuid,
                s.name school_name,
                {$confidentialColumns}
                {$selectDisplayDate}
                LEAST(COUNT(DISTINCT t_attendance.date), COUNT(DISTINCT l_attendance.date))  submitted_attendance_days
            FROM school s
            LEFT JOIN(
                SELECT 
                    school_uuid,
                    date
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                   -- AND attendance_status_oid IS NOT NULL
                    AND date BETWEEN '{$startDate}' AND '{$endDate}'
                 GROUP BY date, school_uuid
            )  t_attendance ON t_attendance.school_uuid = s.uuid 
            LEFT JOIN(
                SELECT 
                    school_uuid,
                    date
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'learner'
                   -- AND attendance_am_status_oid IS NOT NULL AND attendance_pm_status_oid IS NOT NULL 
                    AND date BETWEEN '{$startDate}' AND '{$endDate}'
                 GROUP BY date, school_uuid
            ) l_attendance ON l_attendance.school_uuid = s.uuid 
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
            WHERE s.deleted_at is null
                {$whereClause}
            GROUP BY s.uuid
            ORDER BY submitted_attendance_days ;
        ";
        return DB::select($sql);
    }

    public function getSchoolAttendances($startDate,$endDate,$districtId,$isDistrictOfficerOrAbove){
        $whereClause = "";
        if($districtId != null){
            $whereClause=" AND s.district_id = {$districtId} ";
        }

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
        $sql="
            SELECT 
                s.uuid school_uuid, 
                s.name,
                date,
                {$confidentialColumns}
                CASE WHEN sub4.submission_status IS NULL THEN 'missing_both'
                        ELSE sub4.submission_status END submission_status,
                CASE WHEN sub4.submitted_attendance_days IS NULL THEN 0 
                        ELSE sub4.submitted_attendance_days END submitted_attendance_days 
            FROM    
            (
            SELECT 
                * 
                FROM (
                        SELECT sub2.school_uuid, sub2.date, 
                            CASE 
                                WHEN(sub2.submitted_teacher IS NULL OR sub2.submitted_teacher = 0) AND 
                                    (sub2.submitted_learner IS NULL OR sub2.submitted_learner = 0) THEN 'missing_both'
                                WHEN sub2.submitted_teacher = 1 AND (sub2.submitted_learner IS NULL OR sub2.submitted_learner = 0) THEN 'missing_learner'
                                WHEN (sub2.submitted_teacher IS NULL or sub2.submitted_teacher = 0) AND sub2.submitted_learner = 1 THEN 'missing_teacher'
                                WHEN sub2.submitted_teacher = 1 AND sub2.submitted_learner = 1 THEN 'complete'
                                END submission_status	,
                                SUM(if(sub2.submitted_teacher = 1 AND sub2.submitted_learner = 1, 1, 0)) OVER (PARTITION BY school_uuid) submitted_attendance_days
            
                        FROM (
                        SELECT school_uuid, 
                            sub.date, 
                                sum(submitted_teacher) submitted_teacher, 
                                sum(submitted_learner) submitted_learner
                        FROM (SELECT school_uuid, a.date,
                                CASE 
                                    WHEN entity_type_oid = 'teacher' AND submitted_attendance = 1 THEN 1 
                                    WHEN entity_type_oid = 'learner' THEN NULL 
                                    ELSE 0 END submitted_teacher,
                                CASE
                                    WHEN entity_type_oid = 'learner' AND submitted_attendance = 1 THEN 1 
                                    WHEN entity_type_oid = 'teacher' THEN NULL 
                                    ELSE 0 END submitted_learner
                            FROM
                                (
                                SELECT a.school_uuid,
                                        a.date, a.entity_type_oid,
                                        concat(a.school_uuid, a.date) school_date,
                                        max(a.submitted) submitted_attendance
                                FROM person_attendance a
                                WHERE a.date BETWEEN '{$startDate}' AND '{$endDate}'
                                    AND deleted_at IS NULL
                                GROUP BY a.date, a.school_uuid, a.entity_type_oid) a
                                ) sub	
                        GROUP BY school_uuid, sub.date
                        
                        ) sub2
                    ) sub3
            
                ) sub4
                        
                RIGHT JOIN school s ON sub4.school_uuid = s.uuid
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
                WHERE s.deleted_at is null
                    {$whereClause}

            ORDER BY submitted_attendance_days
        ";
        return DB::select($sql);
    }

    public function schoolDailyAttendances($startDate,$endDate,$districtId,$isDistrictOfficerOrAbove){

        $whereClause = "";
        if($districtId != null){
            $whereClause=" AND district_id = {$districtId} ";
        }

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

        $sql="
            SELECT 
                ca.school_uuid,
                school_name name,
                {$confidentialColumns}
                date,
                CASE 
                    WHEN teachers_reported > 0 AND learners_reported > 0 THEN 'complete' 
                    WHEN teachers_reported > 0 AND learners_reported = 0 THEN 'missing_learner' 
                    WHEN teachers_reported = 0 AND learners_reported > 0 THEN 'missing_teacher' 
                    ELSE 'missing_both' END submission_status,
                SUM(if(teachers_reported > 0 AND learners_reported > 0, 1, 0)) OVER (PARTITION BY school_uuid) submitted_attendance_days
            FROM cache_attendance_by_school_date ca
            LEFT JOIN (
                SELECT
                    t.school_uuid,
                    CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) school_leader_name,
                    IFNULL(p.phone_1,p.phone_2) phone_number
                FROM teacher t
                LEFT JOIN person p ON p.uuid = t.person_uuid
                WHERE (teacher_role_oid = 'head_teacher' OR teacher_role_oid = 'vice_principal')
                GROUP BY school_uuid
            ) s_leader ON s_leader.school_uuid = ca.school_uuid
            WHERE date BETWEEN '{$startDate}' AND '{$endDate}'
            {$whereClause}
            ORDER BY submitted_attendance_days
        ";
        return DB::select($sql);
    }

    public function getWeeklySchoolAttendanceMonitoring($endDate,$districtId,$isDistrictOfficerOrAbove){
        $startDate = Carbon::parse($endDate)->subDays(13)->toDateString();
        
        $rangeStartDate = Carbon::parse($endDate)->subDays(6)->toDateString();
        $dateRange = array();
        for($i=0;$i<=6;$i++){
            $dateRange[] = Carbon::parse($rangeStartDate)->addDays($i)->toDateString();
        }

        //school attendance query
        $schoolsAttendanceCollection = collect($this->schoolDailyAttendances($startDate,$endDate,$districtId,$isDistrictOfficerOrAbove));
        $schools = $schoolsAttendanceCollection->unique('school_uuid');        

        $schoolTeachersProfileCompletionTable = array();
        $schools->each(function ($item) use(&$schoolTeachersProfileCompletionTable,$schoolsAttendanceCollection,$dateRange){

                $schoolAttendanceDateStatus = $schoolsAttendanceCollection->where('school_uuid',$item->school_uuid);
                $attendanceStatusArray = array();

                $day = 1;
                foreach ($dateRange as $dr) {
                    $status = $schoolAttendanceDateStatus->where('date','=',$dr)->first();
                    $attendanceStatusArray['day_'.$day] = ($status == null)? 'missing_both' : $status->submission_status;
                    $day ++;
                }

                 array_push($schoolTeachersProfileCompletionTable,array_merge([
                    "uuid" => $item->school_uuid,
                    "school_name" => $item->name,
                    "school_leader_name" => $item->school_leader_name,
                    "school_leader_phone_number" => $item->school_leader_phone_number,
                    "submitted_attendance_days" => $item->submitted_attendance_days
                ],$attendanceStatusArray));
        });


        $attendanceMissingTableArray = array();
        $attendaceCollection = collect($schoolTeachersProfileCompletionTable);
        $attendaceCollection->where('day_7','!=','complete')
            ->each(function ($item) use(&$attendanceMissingTableArray){
                array_push($attendanceMissingTableArray,$item);
            });

        return [  
            "schoolAttendanceMonitoringData"=> $schoolTeachersProfileCompletionTable,
            "attendanceMissingTable" => $attendanceMissingTableArray
        ];
    }

}