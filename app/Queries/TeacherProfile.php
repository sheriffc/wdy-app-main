<?php

namespace App\Queries;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeacherProfile{

    public function teacherDetails($personUUID){
        $sql="
            SELECT
                p.uuid,
                CONCAT_WS(', ',p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) teacher_name,
                p.nin,
                DATE_FORMAT(p.date_of_birth,'%d %M %Y') date_of_birth,
                CONCAT_WS(' / ', CONCAT('0', p.phone_1), CONCAT('0', p.phone_2)) phone_number,
                p.address,
                ols.item_name sex,
                t.nassit_number,
                CASE 
                    WHEN p.fp_li_uuid is not null 
                        OR p.fp_lt_uuid is not null
                        OR p.fp_ri_uuid is not null
                        OR p.fp_rt_uuid is not null
                    THEN 'Yes'
                ELSE 'No' END fingerprint_registered,
                t.pin,
                IF(ole.item_name IS NOT NULL,CONCAT_WS(' ', ole.item_name, 'Teacher'),'') employment_status,
                DATE_FORMAT(photo.created_at,'%Y-%m-%d') media_created_at,
                photo.portrait_photo
                FROM person p
                LEFT JOIN teacher t ON t.person_uuid = p.uuid
                LEFT JOIN option_list ols ON ols.list_name = 'sex' AND ols.item_id = p.sex_oid
                LEFT JOIN option_list ole ON ole.list_name = 'employment_status' AND ole.item_id = t.employment_status_oid
                LEFT JOIN (
                    SELECT 
                        ref_uuid,
                        base64_data portrait_photo,
                        created_at
                    FROM media_photo  	
                ) photo ON photo.ref_uuid = p.uuid
            WHERE p.uuid ='{$personUUID}' 
            ORDER BY photo.created_at DESC
            LIMIT 1
        ";
        return DB::selectOne($sql);
    }

    public function getTeacherSchoolGroupTable($personUUID,$isDistrictOfficerOrAbove){
        $sql="
            SELECT
                s.uuid school_uuid,
                s.name school_name,
                s.payroll_sid,
                sg.school_group_name,
                ol.item_name school_group_level,
                sg.active,
                sg.academic_year,
                sg_learners.count_learners
            FROM school_group sg
            LEFT JOIN school s ON s.uuid = sg.school_uuid
            LEFT JOIN option_list ol ON ol.list_name = 'school_group_level' AND ol.item_id = sg.school_group_level_oid
            LEFT JOIN teacher t ON t.uuid = sg.teacher_uuid
            LEFT JOIN(
                SELECT
                    sg.uuid,
                    COUNT(sle.learner_uuid) count_learners
                FROM school_group sg
                LEFT JOIN school_learner_enrolment sle ON sle.school_group_uuid = sg.uuid
                WHERE sg.deleted_at IS NULL
                    AND sle.deleted_at IS NULL
                GROUP BY sg.uuid
            ) sg_learners ON sg_learners.uuid = sg.uuid
            WHERE s.deleted_at IS NULL
                AND t.person_uuid = '{$personUUID}'    
        ";
        return DB::select($sql);
    }

    public function getTeacherSchoolData($personUUID,$isDistrictOfficerOrAbove){
        $collection = collect($this->getTeacherSchoolGroupTable($personUUID,$isDistrictOfficerOrAbove));

        $uniqueSchools = $collection->unique('school_uuid');

        $payrolSchools = $uniqueSchools->where('payroll_sid','!=',null)->flatten();
        $wideyaSchools = $uniqueSchools->where('payroll_sid','=',null)->flatten();

        $currentClasses = $collection->where('active','=',true)->flatten();
        $previousClasses = $collection->where('active','=',false)->flatten();

        return [
            "wideyaSchools" => $wideyaSchools,
            "payrollSchools" => $payrolSchools,
            "currentClasses" => $currentClasses,
            "previousClasses" => $previousClasses
        ];
    }

    public function getTeacherLearners($personUUID,$isDistrictOfficerOrAbove){

        $confidentialColumns = "
            null learner_name,
        ";

        if($isDistrictOfficerOrAbove){
            $confidentialColumns=" 
                CONCAT_WS(', ',p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) learner_name,
            ";
        }

        $sql = "
            SELECT
                sg.uuid,
                $confidentialColumns
                olm.item_name maternal_status,
                dsv.item_name vision, 
                dsh.item_name hearing, 
                dsm.item_name mobility, 
                dsc.item_name cognition, 
                dss.item_name selfcare, 
                dscom.item_name communication
            FROM school_group sg
            LEFT JOIN school_learner_enrolment sle ON sle.school_group_uuid = sg.uuid
            LEFT JOIN teacher t ON t.uuid = sg.teacher_uuid
            LEFT JOIN learner l ON l.uuid = sle.learner_uuid
            LEFT JOIN person p ON p.uuid = l.person_uuid
            LEFT JOIN option_list olm ON olm.list_name = 'maternal_status' AND olm.item_id = l.maternal_status_oid
            LEFT JOIN option_list dsv ON dsv.list_name = 'disability_severity' AND dsv.item_id = l.disability_severity_oid_vision
            LEFT JOIN option_list dsh ON dsh.list_name = 'disability_severity' AND dsh.item_id = l.disability_severity_oid_hearing
            LEFT JOIN option_list dsm ON dsm.list_name = 'disability_severity' AND dsm.item_id = l.disability_severity_oid_mobility
            LEFT JOIN option_list dsc ON dsc.list_name = 'disability_severity' AND dsc.item_id = l.disability_severity_oid_cognition
            LEFT JOIN option_list dss ON dss.list_name = 'disability_severity' AND dss.item_id = l.disability_severity_oid_selfcare
            LEFT JOIN option_list dscom ON dscom.list_name = 'disability_severity' AND dscom.item_id = l.disability_severity_oid_communication
            WHERE sg.deleted_at IS NULL
                AND sle.deleted_at IS NULL
                AND t.person_uuid = '{$personUUID}'
        ";
        return DB::select($sql);
    }

    public function getTeacherAttendanceData($personUUID,$startDate,$endDate){
        $sql="
        SELECT
            date,
            DATE_FORMAT(date, '%a, %D  %b %Y') format_date,
            s.name school_name,
            s.uuid school_uuid,
            CASE WHEN attendance_status_oid = 'present' THEN 1 ELSE 0 END attendance_present,
            CASE WHEN attendance_status_oid = 'late' THEN 1 ELSE 0 END attendance_late,
            CASE WHEN attendance_status_oid = 'absent' THEN 1 ELSE 0 END attendance_absent,
            CASE WHEN absent_reason_oid = 'no_valid_reason_absent' 
                    OR absent_reason_oid = 'no_valid_reason_early_departure'
                THEN 1 ELSE 0 END unauthorised_absences,
            CASE WHEN absent_reason_oid != 'no_valid_reason_absent' 
                    AND absent_reason_oid != 'no_valid_reason_early_departure'
                THEN 1 ELSE 0 END excused_absences,
            pa.attendance_status_oid,
            olas.item_name attendance_status,
            pa.absent_reason_oid,
            olar.item_name absent_reason,
            pa.biometric_method_oid,
	        olbm.item_name biometric_method
        FROM person_attendance pa
        LEFT JOIN school s ON s.uuid = pa.school_uuid  
        LEFT JOIN option_list olas ON olas.item_id = pa.attendance_status_oid AND olas.list_name = 'attendance_status'
        LEFT JOIN option_list olar ON olar.item_id = pa.absent_reason_oid AND olar.list_name = 'absent_reason_teacher'
        LEFT JOIN option_list olbm ON olbm.item_id = pa.biometric_method_oid AND olbm.list_name = 'biometric_method'
        WHERE pa.submitted = 1 
            AND pa.deleted_at IS NULL
            AND pa.entity_type_oid = 'teacher'
            AND pa.date BETWEEN '{$startDate}' AND '{$endDate}'
            AND pa.person_uuid = '{$personUUID}'
        ";
        return DB::select($sql);
    }

    public function getTeacherAttendanceSummary($personUUID){
        $endDate = Carbon::now()->toDateString();
        $startDate = Carbon::now()->subDays(30)->toDateString();

        $collection = collect($this->getTeacherAttendanceData($personUUID,$startDate,$endDate));
        $attendanceSummary=[
            "excusedAbsences" => $collection->sum("excused_absences"),
            "unauthorisedAbsences" => $collection->sum("unauthorised_absences"),
            "lateArrivals" => $collection->sum("attendance_late"),
            "absenteeismRate" => round(($collection->sum("attendance_absent")/30*100))
        ];

        $dateLabels = [];
        $absentData = [];
        $lateData = [];
        $presentData = [];

        $daysCounter = 1;

        for ($i=0; $i < 4; $i++) { 
            $dateFrom = Carbon::now()->subDays($daysCounter+6);
            $dateTo = Carbon::now()->subDays($daysCounter);

            $weekAttendance = $collection->whereBetween("date",[$dateFrom->toDateString(),$dateTo->toDateString()]);
            array_push($dateLabels,"{$dateFrom->format("M d")} - {$dateTo->format("M d")}"); 
            array_push($absentData, $weekAttendance->sum("attendance_absent")); 
            array_push($lateData, $weekAttendance->sum("attendance_late")); 
            array_push($presentData, $weekAttendance->sum("attendance_present"));
            $daysCounter+=6;
        }

        $weeklyAttendanceTrends =[
            "weekLabels" => array_reverse($dateLabels),
            "presentAttendanceList" => array_reverse($presentData),
            "lateAttendanceList" => array_reverse($lateData),
            "absentAttendanceList" => array_reverse($absentData)
        ];

        return[
            "attendanceSummaryChart"=>$attendanceSummary,
            "weeklyAttendance"=>$weeklyAttendanceTrends
        ];
    }

    public function getTeacherAttendanceRecords($personUUID,$startDate,$endDate){
        $collection = collect($this->getTeacherAttendanceData($personUUID,$startDate,$endDate));
        $summary = [
            "reportsSubmitted" => $collection->count(),
            "present" => $collection->sum("attendance_present"),
            "late" => $collection->sum("attendance_late"),
            "absent" => $collection->sum("attendance_absent"),
            "unauthorisedAbsences" => $collection->sum("unauthorised_absences")
        ];
        return[
            "attendanceRecords" => $collection->flatten(),
            "summary" => $summary
        ];
    }

    public function getTeacherTimeTable($personUUID){
        $sql="
            SELECT 
                day_of_the_week_oid,
                IFNULL(tt.school_subject_other,ols.item_name) school_subject,
                tt.start_time,
                tt.end_time 
            FROM teacher_timetable tt 
            LEFT JOIN teacher t ON t.uuid = tt.teacher_uuid 
            LEFT JOIN option_list ols ON ols.item_id = tt.school_subject_oid AND ols.list_name = 'school_subject'
                WHERE t.person_uuid = '{$personUUID}'
        ";
        return DB::select($sql);
    }

    public function getTeacherAbsentReasons(){
        $sql="
            SELECT 
                item_id,
                item_name 
            FROM option_list ol 
            WHERE list_name = 'absent_reason_teacher'
            ORDER BY item_id       
        ";
        return DB::select($sql);
    }
}