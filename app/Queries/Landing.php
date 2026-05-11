<?php

namespace App\Queries;

use App\Common\Queries\DbQueries;
use Illuminate\Support\Facades\DB;

class Landing
{
    public function getMaxDateTeacherAttendance(){
        $sql="
            SELECT
                MAX(date) max_attendance_date
            FROM person_attendance
            WHERE entity_type_oid = 'teacher'
                AND submitted = 1
                AND deleted_at IS NULL
            LIMIT 1
        ";
        return DB::selectOne($sql);
    }

    public function getDropdownDatesTeacherAttendance($inputDate){
        $sql = "
            SELECT
                date,
                DATE_FORMAT(date, '%W, %D  %b %Y') format_date
            FROM person_attendance
            WHERE entity_type_oid = 'teacher'
                AND submitted = 1
                AND deleted_at IS NULL
                AND ( date BETWEEN DATE_SUB(?,INTERVAL 10 DAY) AND DATE_ADD(?,INTERVAL 10 DAY) )
            GROUP BY date
            ORDER BY date DESC
            LIMIT 20
        ";
        return DB::select($sql,[$inputDate,$inputDate]);
    }

    function getTodayHeadlineTotals($date, $districtId, $academicYear = null){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" AND s.district_id = {$districtId} ";
        }
        $yearClause = $academicYear
            ? intval($academicYear)
            : "(SELECT academic_year FROM school_academic_year WHERE active = 1 LIMIT 1)";

        // SET @date = '2023-03-02';
        $sql = "
            SELECT
                COUNT(s.uuid) schools_total,
                COUNT(s_reported.school_uuid) schools_reported,
                SUM(IFNULL(s_teachers.count,0)) teachers_total,
                SUM(IFNULL(s_learners.count,0)) learners_total,
                SUM(IFNULL(t_present.count,0) + IFNULL(t_absent.count,0)) teachers_reported,
                SUM(IFNULL(t_present.count,0)) teachers_present,
                SUM(IFNULL(t_absent.count,0)) teachers_absent,
                SUM(IFNULL(l_reported.count,0)) learners_reported
            FROM school s
            LEFT JOIN (
                SELECT
                    school_uuid
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                    AND date = @date
                GROUP BY school_uuid
            ) AS s_reported ON s.uuid = s_reported.school_uuid
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
                    sg.school_uuid,
                    COUNT(DISTINCT sle.learner_uuid) as count
                FROM school_learner_enrolment sle
                INNER JOIN school_group sg ON sg.uuid = sle.school_group_uuid
                WHERE sle.deleted_at IS NULL
                    AND sle.academic_year = {$yearClause}
                GROUP BY sg.school_uuid
            ) s_learners ON s_learners.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                    AND date = @date
                    AND (attendance_status_oid = 'present' OR attendance_status_oid = 'late')
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
                    AND attendance_status_oid = 'absent'
                GROUP BY school_uuid
            ) AS t_absent ON s.uuid = t_absent.school_uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'learner'
                    AND date = @date
                    -- AND attendance_am_status_oid IS NOT NULL -- *performance bottleneck*
                    -- AND attendance_pm_status_oid IS NOT NULL -- *performance bottleneck*
                GROUP BY school_uuid
            ) AS l_reported ON s.uuid = l_reported.school_uuid
            WHERE s.created_at < DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY)
                AND (s.deleted_at IS NULL OR s.deleted_at >= DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY))
            {$districtWhereClause}
        ";
        $sql = str_replace("@date","'$date'",$sql);

        return DB::selectOne($sql);
    }

    function getSchoolsTable($date, $districtId, $academicYear = null){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" AND s.district_id = {$districtId} ";
        }
        $yearClause = $academicYear
            ? intval($academicYear)
            : "(SELECT academic_year FROM school_academic_year WHERE active = 1 LIMIT 1)";

        // SET @date = '2023-03-02';
        $sql = "
            SELECT
                s.uuid,
                s.payroll_sid,
                s.emis_id,
                s.lat,
                s.lng,
                s.name,
                IFNULL(s_teachers.count,0) teachers_total,
                IFNULL(t_present.count,0) + IFNULL(t_absent.count,0) teachers_reported,
                IFNULL(t_present.count,0) teachers_present,
                IFNULL(t_absent.count,0) teachers_absent,
                IFNULL(s_learners.count,0) learners_total,
                IFNULL(l_reported.count,0) learners_reported
            FROM school s
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
                    sg.school_uuid,
                    COUNT(DISTINCT sle.learner_uuid) as count
                FROM school_learner_enrolment sle
                INNER JOIN school_group sg ON sg.uuid = sle.school_group_uuid
                WHERE sle.deleted_at IS NULL
                    AND sle.academic_year = {$yearClause}
                GROUP BY sg.school_uuid
            ) s_learners ON s_learners.school_uuid = s.uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'teacher'
                    AND date = @date
                    AND (attendance_status_oid = 'present' OR attendance_status_oid = 'late')
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
                    AND attendance_status_oid = 'absent'
                GROUP BY school_uuid
            ) AS t_absent ON s.uuid = t_absent.school_uuid
            LEFT JOIN (
                SELECT
                    school_uuid,
                    COUNT(*) as count
                FROM person_attendance
                WHERE submitted = 1
                    AND deleted_at IS NULL
                    AND entity_type_oid = 'learner'
                    AND date = @date
                    -- AND attendance_am_status_oid IS NOT NULL -- *performance bottleneck*
                    -- AND attendance_pm_status_oid IS NOT NULL -- *performance bottleneck*
                GROUP BY school_uuid
            ) AS l_reported ON s.uuid = l_reported.school_uuid
            WHERE s.created_at < DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY)
                AND (s.deleted_at IS NULL OR s.deleted_at >= DATE_ADD(CAST(@date AS DATETIME), INTERVAL 1 DAY))
              --  AND s.lat > 0
            {$districtWhereClause}
            ORDER BY teachers_reported ASC
        ";
        $sql = str_replace("@date","'$date'",$sql);

        return DB::select($sql);
    }

    function getSchoolsAttendanceChart($inputDate, $districtId, $academicYear = null){
        $schoolDetails = $this->getSchoolsTable($inputDate, $districtId, $academicYear);
        $output = [];
        foreach($schoolDetails as $school){
//            $reported = $school->present + $school->absent;
//            $notReported = ($school->staffs < $reported) ? 0 : $school->staffs - $school->present - $school->absent;
            $output[] = [
                "id"=>strval( $school->uuid ),
                "payroll_sid"=>$school->payroll_sid,
                "emis_id"=>$school->emis_id,
                "name"=>$school->name,
                "lat"=>$school->lat,
                "lon"=>$school->lng,
                "teachers_total"=>$school->teachers_total,
                "teachers_not_reported"=>$school->teachers_total - $school->teachers_reported,
                "teachers_reported"=>$school->teachers_reported,
                "teachers_present"=>$school->teachers_present,
                "teachers_absent"=>$school->teachers_absent,
                "learners_total"=>$school->learners_total,
                "learners_reported"=>$school->learners_reported,
                "learners_not_reported"=>$school->learners_total - $school->learners_reported,
            ];
        }
        return $output;
    }

    function getTeacherAttendanceBarchart($startDate,$endDate,$districtId){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" AND s.district_id = {$districtId} ";
        }

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
            LEFT JOIN school s ON s.created_at < DATE_ADD(CAST(dr.date AS DATETIME), INTERVAL 1 DAY)
                {$districtWhereClause}
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
                GROUP BY date, school_uuid, attendance_status_oid
            ) AS attendance_breakdown ON dr.date = attendance_breakdown.date AND s.uuid = attendance_breakdown.school_uuid
            -- TODO: the handling of created_at/deleted_at checks is very problematic in the total teachers count below
            LEFT JOIN (
                SELECT
                    school_uuid,
                    created_at,
                    COUNT(*) as count
                FROM teacher
                WHERE deleted_at IS NULL
                GROUP BY school_uuid
            ) AS s_teachers ON s_teachers.school_uuid = s.uuid
                AND s_teachers.created_at < DATE_ADD(CAST(dr.date AS DATETIME), INTERVAL 1 DAY)
            GROUP BY dr.date
        ";
        return DB::select($sql,[$startDate,$endDate]);
    }

    public function classGenderRatio($districtId, $academicYear = null){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" AND s.district_id = {$districtId} ";
        }
        $yearClause = $academicYear
            ? intval($academicYear)
            : "(SELECT academic_year FROM school_academic_year WHERE active = 1 LIMIT 1)";
        $sql="
            SELECT
                p.sex_oid sex,
                IFNULL(sgl.item_name, 'Not Enrolled') `class_level`,
                COUNT(*) count
            FROM learner l
            LEFT JOIN person p ON l.person_uuid = p.uuid
            LEFT JOIN school_learner_admission sla on l.uuid = sla.learner_uuid
            LEFT JOIN school s on sla.school_uuid = s.uuid
            LEFT JOIN school_learner_enrolment sle ON l.uuid = sle.learner_uuid
                AND sle.deleted_at IS NULL
                AND sle.academic_year = {$yearClause}
            LEFT JOIN school_group sg on sle.school_group_uuid = sg.uuid
            LEFT JOIN option_list sgl ON  sgl.list_name = 'school_group_level' AND sg.school_group_level_oid = sgl.item_id
            WHERE p.sex_oid IS NOT NULL AND sla.deleted_at IS NULL
                {$districtWhereClause}
            GROUP BY p.sex_oid, sgl.item_name
            ORDER BY `class_level`, `sex`
        ";
        return DB::select($sql);
    }

    public function learnersMaternalStatusTable($districtId){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" AND s.district_id = {$districtId} ";
        }
        $sql="
            SELECT CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name)) `name`,
                    l.maternal_status_oid,
                    ms.item_name `maternal_status`,
                    d.name `district`,
                    s.uuid `school_uuid`,
                    s.name `school`,
                    s_leader.school_leader_name `school_leader_name`,
                    s_leader.phone_number `school_leader_phone`,
                    CONCAT_WS(', ', g.last_name, CONCAT_WS(' ', g.first_name, g.middle_name)) `guardian_name`,
                    IFNULL(g.phone_1, g.phone_2) `guardian_phone`

            FROM learner l
            LEFT JOIN option_list ms ON ms.list_name = 'maternal_status' AND l.maternal_status_oid = ms.item_id
            LEFT JOIN person p on l.person_uuid = p.uuid
            LEFT JOIN school_learner_admission sla ON l.uuid = sla.learner_uuid
            LEFT JOIN school s on sla.school_uuid = s.uuid
            LEFT JOIN district_office d on s.district_id = d.district_id
            LEFT JOIN person g ON l.guardian_person_uuid = g.uuid
            LEFT JOIN (SELECT
                        t.school_uuid,
                        max(CONCAT_WS(', ', p.last_name, CONCAT_WS(' ', p.first_name, p.middle_name))) school_leader_name,
                        max(IFNULL(p.phone_1,p.phone_2)) phone_number
                    FROM teacher t
                    LEFT JOIN person p ON p.uuid = t.person_uuid
                    WHERE (teacher_role_oid = 'head_teacher' OR teacher_role_oid = 'vice_principal')
                    GROUP BY school_uuid) s_leader ON s_leader.school_uuid = s.uuid

            WHERE p.sex_oid = 'female'
                {$districtWhereClause}
                AND sla.deleted_by IS NULL
        ";
        return DB::select($sql);
    }

    public function getSchoolsMaternalStatustable($districtId){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" WHERE district_id = {$districtId} ";
        }
        $sql="
            SELECT
                uuid,
                name school_name,
                lat,
                lng lon,
                district_id,
                maternal_status_not_complete,
                maternal_status_none,
                maternal_status_mother,
                maternal_status_pregnant,
                maternal_status_pregnant_mother
            FROM cache_school_info
            {$districtWhereClause}
        ";
        return DB::select($sql);
    }

    public function getSchoolsMaternalStatusChart($districtId){
        $collection = collect($this->getSchoolsMaternalStatustable($districtId));
        $maternalStatusRates =[
            "no_status" => $collection->sum("maternal_status_not_complete"),
            "none" => $collection->sum("maternal_status_none"),
            "mother" => $collection->sum("maternal_status_mother"),
            "pregnant" => $collection->sum("maternal_status_pregnant"),
            "pregnant_mother" => $collection->sum("maternal_status_pregnant_mother")
        ];
        return [
            "maternalStatusRates" => $maternalStatusRates,
            "maternalStatusGeoLocation" => $collection
        ];
    }

    public function getMaternalStatusChart($districtId){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" AND s.district_id = {$districtId} ";
        }
        $sql="
        SELECT
                COALESCE(l.maternal_status_oid,'no_status') maternal_status_oid,
                count(*) `count`

        FROM learner l
        LEFT JOIN person p ON l.person_uuid = p.uuid
        LEFT JOIN school_learner_admission sla on l.uuid = sla.learner_uuid
        LEFT JOIN school s on sla.school_uuid = s.uuid
        WHERE p.sex_oid = 'female' and sla.deleted_by IS NULL
            {$districtWhereClause}
        GROUP BY l.maternal_status_oid
        ";
        return DB::select($sql);
    }

    public function getMaternalStatusGeoMap($districtId){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" AND s.district_id = {$districtId} ";
        }
        $sql="
            SELECT
                s.uuid,
                s.name school_name,
                s.district_id,
                s.lat,
                s.lng lon,
                sum(if(l.maternal_status_oid = 'moth', 1, 0)) count_mother,
                sum(if(l.maternal_status_oid = 'preg', 1, 0)) count_pregnant

            FROM school s
            LEFT JOIN school_learner_admission sla ON s.uuid = sla.school_uuid
            LEFT JOIN learner l on sla.learner_uuid = l.uuid
            WHERE s.deleted_at is null
             {$districtWhereClause}
            group by s.uuid
        ";
        return DB::select($sql);
    }

    public function getLearnerWithMaternalStatusChart($districtId){
        $collection = collect($this->learnersMaternalStatusTable($districtId));
        $maternalStatus = DbQueries::getOptionList('maternal_status');

        $maternalStatusRates = array();
        foreach($maternalStatus as $key){
            $maternalStatusRates[$key->item_id] = $collection->where('maternal_status_oid','=',$key->item_id)->count();
        }

        $schoolLocations = array();

        $collection->unique("school_uuid")->each(function ($item) use(&$schoolLocations,$collection){
            array_push($schoolLocations,[
                "school_uuid"=>$item->school_uuid,
                "count"=> $collection->where('school_uuid','=',$item->school_uuid)->where('maternal_status_oid','!=','none')->count()
            ]);
        });

        return [$maternalStatusRates,$schoolLocations];
    }

    public function getLearnerAbsenteeismRates($districtId){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" WHERE district_id = {$districtId} ";
        }
        $sql="
            SELECT
                ROUND(SUM(learners_male_absent) / SUM(learners_male),2) males,
                ROUND(SUM(learners_female_absent) / SUM(learners_female),2) females,
                ROUND(SUM(LEAST(maternal_am_absent_mother,maternal_pm_absent_mother)) / SUM(maternal_learners_mothers),2) mother,
                ROUND(SUM(LEAST(maternal_am_absent_pregnant,maternal_pm_absent_pregnant)) / SUM(maternal_learners_pregnant),2) pregnant,
                ROUND(SUM(LEAST(maternal_am_absent_pregnant_mother,maternal_pm_absent_pregnant_mother)) / SUM(maternal_learners_pregnant_mother),2) pregnant_mother,
                ROUND(SUM(maternal_absent_none) / SUM(maternal_learners_status_none),2) none
            FROM cache_attendance_by_school_date
            {$districtWhereClause}
        ";
        return DB::selectOne($sql);
    }

    public function learnersWithSpecialTable($districtId){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" WHERE district_id = {$districtId} ";
        }
        $sql="
            SELECT
                uuid,
                name school_name,
                lat ,
                lng lon,
                district_id,
                learners_total,
                learners_screened_special_needs,
                disability_learners_total,
                learners_disability_vision,
                learners_disability_vision_severity_1,
                learners_disability_vision_severity_2,
                learners_disability_vision_severity_3,
                learners_disability_hearing,
                learners_disability_hearing_severity_1,
                learners_disability_hearing_severity_2,
                learners_disability_hearing_severity_3,
                learners_disability_mobility,
                learners_disability_mobility_severity_1,
                learners_disability_mobility_severity_2,
                learners_disability_mobility_severity_3,
                learners_disability_cognition,
                learners_disability_cognition_severity_1,
                learners_disability_cognition_severity_2,
                learners_disability_cognition_severity_3,
                learners_disability_selfcare,
                learners_disability_selfcare_severity_1,
                learners_disability_selfcare_severity_2,
                learners_disability_selfcare_severity_3,
                learners_disability_communication,
                learners_disability_communication_severity_1,
                learners_disability_communication_severity_2,
                learners_disability_communication_severity_3,
                learners_condition_albinism,
                learners_condition_epilepsy,
                learners_condition_dwarfism

            FROM cache_school_info
            {$districtWhereClause}
        ";
        return DB::select($sql);
    }

    public function getLearnerWithSpecialNeedsChart($districtId){
        $collection = collect($this->learnersWithSpecialTable($districtId));
        $conditions = [
            "albinism" => $collection->sum("learners_condition_albinism"),
            "epilepsy" => $collection->sum("learners_condition_epilepsy"),
            "dwarfism" => $collection->sum("learners_condition_dwarfism")
        ];
        $disabilitySeverity = [
            "some_difficulty" => [
                $collection->sum("learners_disability_vision_severity_1"),
                $collection->sum("learners_disability_hearing_severity_1"),
                $collection->sum("learners_disability_cognition_severity_1"),
                $collection->sum("learners_disability_selfcare_severity_1"),
                $collection->sum("learners_disability_communication_severity_1"),
                $collection->sum("learners_disability_mobility_severity_1")
            ],
            "lot_of_difficulty" => [
                $collection->sum("learners_disability_vision_severity_2"),
                $collection->sum("learners_disability_hearing_severity_2"),
                $collection->sum("learners_disability_cognition_severity_2"),
                $collection->sum("learners_disability_selfcare_severity_2"),
                $collection->sum("learners_disability_communication_severity_2"),
                $collection->sum("learners_disability_mobility_severity_2")
            ],
            "cannot_do" => [
                $collection->sum("learners_disability_vision_severity_3"),
                $collection->sum("learners_disability_hearing_severity_3"),
                $collection->sum("learners_disability_cognition_severity_3"),
                $collection->sum("learners_disability_selfcare_severity_3"),
                $collection->sum("learners_disability_communication_severity_3"),
                $collection->sum("learners_disability_mobility_severity_3")
            ]
        ];
        $totalLearners = $collection->sum("learners_total");
        $totalLearnerCommonConditon = $collection->sum("learners_condition_dwarfism") + $collection->sum("learners_condition_epilepsy") + $collection->sum("learners_condition_albinism");
        $highlights =[
            "learnerScreenedRate" => round($collection->sum("learners_screened_special_needs") / $totalLearners *100,0),
            "learnerDifficultyRate" => round($collection->sum("disability_learners_total") / $totalLearners *100,0),
            "learnerDifficultyCount" =>number_format($collection->sum("disability_learners_total")),
            "commonConditionsRate" => $totalLearnerCommonConditon." out of ". number_format($totalLearners),
            "commonConditionsCount" => number_format($totalLearnerCommonConditon),
            "learnerTotal" => number_format($totalLearners)



        ];
        return [
            "highlights" => $highlights,
            "commonConditions" => $conditions,
            "disabilitySeverity" => $disabilitySeverity,
            "specialNeedsSchools" => $collection
        ];
    }

    public function specialNeedsAttendanceAbsenteeismChart($districtId){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" WHERE district_id = {$districtId} ";
        }
        $sql="
            SELECT
                school_uuid,
                district_id,
                lat,
                lng lon,
                school_name,
                SUM(disability_vision_learners) disability_vision_learners, 
                SUM(disability_hearing_learners) disability_hearing_learners, 
                SUM(disability_mobility_learners) disability_mobility_learners, 
                SUM(disability_cognition_learners) disability_cognition_learners, 
                SUM(disability_selfcare_learners) disability_selfcare_learners,
                SUM(disability_communication_learners) disability_communication_learners,
                SUM(disability_vision_absent) vision,
                SUM(disability_hearing_absent) hearing,
                SUM(disability_mobility_absent) mobility,
                SUM(disability_cognition_absent) cognition,
                SUM(disability_communication_absent) communication,
                SUM(disability_selfcare_absent) selfcare
            FROM cache_attendance_by_school_date
            {$districtWhereClause}
            GROUP BY school_uuid
        ";
        return DB::select($sql);
    }

    public function getSpecialNeedsAttendanceAbsenteeismData($districtId){
        $collection = collect($this->specialNeedsAttendanceAbsenteeismChart($districtId));
        $specialNeedsAttendanceAbsenteeismChart = [
            "hearing"=> round($collection->sum("hearing") / $collection->sum("disability_hearing_learners") *100,0),
            "selfcare"=>round($collection->sum("selfcare") / $collection->sum("disability_selfcare_learners") *100,0),
            "cognition"=>round($collection->sum("cognition") / $collection->sum("disability_cognition_learners") *100,0),
            "communication"=>round($collection->sum("communication") / $collection->sum("disability_communication_learners") *100,0),
            "vision"=>round($collection->sum("vision") / $collection->sum("disability_vision_learners") *100,0),
            "mobility"=>round($collection->sum("mobility") / $collection->sum("disability_mobility_learners") *100,0)
        ];

        return[
            "specialNeedsAttendanceAbsenteeismChart"=>$specialNeedsAttendanceAbsenteeismChart,
            "specialNeedsAbsenteeismTable"=> $collection
        ];
    }

    public function getAtRiskLearnersTable($districtId){
        /**Todo:might need add filter for academic year */
        /** Todo: add filter for removed learners which are duplicates or mistake */
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" WHERE district_id = {$districtId} ";
        }
        $sql = "
        SELECT 
            l.uuid ,
            COALESCE(ROUND((
            learners_absent.absent_days/(SELECT COUNT(DISTINCT date) AS total_count
                FROM person_attendance
                WHERE entity_type_oid = 'learner' 
                AND deleted_at is NULL 
            ))*100),0) percentage
        FROM learner l 
       -- LEFT JOIN school_learner_admission sla ON sla.learner_uuid = l.uuid AND sla.end_reason_learner_oid != 'duplicate' OR sla.end_reason_learner_oid != 'mistake'
        LEFT JOIN(
            SELECT
                pa.person_uuid ,
                SUM(
                CASE WHEN pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent' 
                    THEN 1 ELSE 0 END
                ) AS absent_days
            FROM person_attendance pa 
            WHERE pa.entity_type_oid = 'learner'
            AND deleted_at is NULL 
            GROUP BY pa.person_uuid 
        )AS learners_absent ON learners_absent.person_uuid = l.person_uuid 
        ";
        return DB::select($sql);
    }

    public function getAtRiskSchoolsLearners($districtId){
        /**Todo: might need add filter for academic year */
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" AND district_id = {$districtId} ";
        }
        $sql="
            SELECT 
                s.uuid ,
                s.name,
                s.district_id,
                s.lat,
                s.lng lon,
                s_learners.absent_learners_count
            FROM school s 
            LEFT JOIN(
                SELECT
                    pa.school_uuid ,
                    SUM(
                    CASE WHEN pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent' 
                        THEN 1 ELSE 0 END
                    ) AS absent_learners_count
                FROM person_attendance pa 
                WHERE pa.entity_type_oid = 'learner'
                AND pa.deleted_at IS NULL
                GROUP BY pa.school_uuid
            )AS s_learners ON s_learners.school_uuid = s.uuid 
            WHERE s.deleted_at IS NULL 
            GROUP BY s.uuid
            {$districtWhereClause}
        ";
        return DB::select($sql);
    }

    public function getAtRiskSchoolLearnerChart($districtId){
        $collection = collect($this->getAtRiskSchoolsLearners($districtId));
        return[
            "schools"=>$collection
        ];

    }

    public function getAtRiskLearnersChart($districtId){
        $collection = collect($this->getAtRiskLearnersTable($districtId));

        $serverlyAbsent = $collection->where("percentage",">=",50)->count();
        $persistentAbsent = $collection->whereBetween("percentage",[10,49])->count();
        $totalAbsent =  $collection->where("percentage",">=",0)->count();

        return [
            "severly_absent"=> $serverlyAbsent,
            "persistent_absent"=>$persistentAbsent,
            "persistent_percentage"=> round(($persistentAbsent/$totalAbsent)*100,0),
            "serverly_percentage"=> round(($serverlyAbsent/$totalAbsent)*100,0)
        ];
    }

    public function getLearnersRemovedFromSchoolTable($districtId){
        $districtWhereClause = "";
        if($districtId != null){
            $districtWhereClause=" WHERE district_id = {$districtId} ";
        }
        $sql ="
            SELECT 
                uuid,
                name,
                (
                    learners_removed_graduated +
                    learners_removed_transfer +
                    learners_removed_dropout_exams + 
                    learners_removed_dropout_maternal + 
                    learners_removed_dropout_other +
                    learners_removed_unknown 
                ) total_learners,
                learners_removed_graduated,
                learners_removed_transfer, 
                learners_removed_dropout_exams, 
                learners_removed_dropout_maternal, 
                learners_removed_dropout_other, 
                learners_removed_unknown, 
                learners_removed_duplicate, 
                learners_removed_mistake
            FROM cache_school_info
            {$districtWhereClause}
        ";
        return DB::select($sql);
    }

    public function removedLearnersChart($districtId){
        $collection = collect($this->getLearnersRemovedFromSchoolTable($districtId));
      
        $uniqueEndReasons =[
            "Graduated"=>$collection->sum("learners_removed_graduated"), 
            "Transfered"=>$collection->sum("learners_removed_transfer"), 
            "Exam difficulty"=>$collection->sum("learners_removed_dropout_exams"), 
            "Pregnancy/Childcare"=>$collection->sum("learners_removed_dropout_maternal"), 
            "Personal/Other"=>$collection->sum("learners_removed_dropout_other"), 
            "Unknown"=>$collection->sum("learners_removed_unknown")
        ];

        return [
            "endReasonsChart"=> $uniqueEndReasons,
            "removedLearnerSchools"=> $collection
        ];
    }

    public function genderAbseenteeismRate(){
        $sql = "
        SELECT  
            ROUND(SUM(IF(learners_male_absent>0,1,0)) / (SELECT COUNT(*) FROM cache_attendance_by_school_date WHERE learners_absent > 0 OR learners_present > 0 ),1) males,
            ROUND(SUM((IF(learners_female_absent>0,1,0)) /(SELECT COUNT(*) FROM cache_attendance_by_school_date WHERE learners_absent > 0 OR learners_present > 0 )) ,1) females
        FROM cache_attendance_by_school_date;
        ";
        return DB::select($sql);
    }

    public function ageAbseenteeismRate(){
        //Todo: filter out from specific year maybe 200
        //Todo: AND p.date_of_birth is not null (query slow)
        $sql = "
        SELECT
            TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age,
            ROUND(COUNT(pa.date)/(SELECT COUNT(*) FROM person_attendance WHERE entity_type_oid='learner' AND attendance_am_status_oid is not null AND attendance_pm_status_oid is not null)*100) rate
        FROM person_attendance pa
        LEFT JOIN person p ON p.uuid = pa.person_uuid 
        WHERE entity_type_oid='learner' 
            AND attendance_am_status_oid = 'absent' 
            AND attendance_pm_status_oid = 'absent'
            AND pa.deleted_by is null
            AND p.deleted_by is null
            -- Todo: AND p.date_of_birth is not null (query slow)
        GROUP BY age
        ORDER BY age
        ";
        return DB::select($sql);
    }

    public function districtAbseenteeismRate(){
        $sql = "
            SELECT 
                s.district_id,
                do.name,
                COUNT(s.district_id),
                ROUND(COUNT(s.district_id)/(SELECT COUNT(*) FROM person_attendance WHERE entity_type_oid='learner' AND attendance_am_status_oid is not null AND attendance_pm_status_oid is not null)*100,1) rate
            FROM person_attendance pa
            LEFT JOIN school s ON s.uuid = pa.school_uuid
            LEFT JOIN district_office do ON do.district_id = s.district_id
            WHERE entity_type_oid='learner' 
                AND attendance_am_status_oid = 'absent' 
                AND attendance_pm_status_oid = 'absent'
                AND pa.deleted_by is null
            GROUP BY s.district_id
            ORDER BY rate desc
        ";
        return DB::select($sql);
    }

    public function getGenderSeverityAbseenteismRate(){
        $sql = "
            SELECT 
                ROUND(SUM(no_difficulty)/SUM(attendance_days)*100) no_difficulty_rate,
                ROUND(SUM(some_difficulty)/SUM(attendance_days)*100) some_difficulty_rate,
                ROUND(SUM(lot_of_difficulty)/SUM(attendance_days)*100) lot_of_difficulty_rate,
                ROUND(SUM(cannot_do)/SUM(attendance_days)*100) cannot_do_rate,
                ROUND(SUM(males)/SUM(attendance_days)*100) males_rate,
                ROUND(SUM(females)/SUM(attendance_days)*100) females_rate
            FROM (
                SELECT 
                    IF(date is not null,1,0) attendance_days,
                    IF(sum(no_difficulty) > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,0) no_difficulty,
                    IF(sum(some_difficulty) > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,0) some_difficulty,
                    IF(sum(lot_of_difficulty) > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,0) lot_of_difficulty,
                    IF(sum(cannot_do) > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,0) cannot_do,
                    IF(sum(males) > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,0) males,
                    IF(sum(females) > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,0) females
                FROM person_attendance pa
                LEFT JOIN (
                    SELECT 
                        l.person_uuid,
                        CASE 
                            WHEN p.sex_oid = 'male' THEN 1 
                        ELSE 0 END males,
                        CASE 
                            WHEN p.sex_oid = 'female' THEN 1 
                        ELSE 0 END females,
                        CASE 
                            WHEN disability_severity_oid_vision = '0_no_difficulty' 
                                OR disability_severity_oid_hearing = '0_no_difficulty'
                                OR disability_severity_oid_mobility = '0_no_difficulty'
                                OR disability_severity_oid_cognition = '0_no_difficulty'
                                OR disability_severity_oid_selfcare = '0_no_difficulty'
                                OR disability_severity_oid_communication = '0_no_difficulty'
                            THEN 1 
                        ELSE 0 END no_difficulty,
                        CASE 
                            WHEN disability_severity_oid_vision = '1_some_difficulty' 
                                OR disability_severity_oid_hearing = '1_some_difficulty'
                                OR disability_severity_oid_mobility = '1_some_difficulty'
                                OR disability_severity_oid_cognition = '1_some_difficulty'
                                OR disability_severity_oid_selfcare = '1_some_difficulty'
                                OR disability_severity_oid_communication = '1_some_difficulty'
                            THEN 1 
                        ELSE 0 END some_difficulty,
                        CASE 
                            WHEN disability_severity_oid_vision = '2_lot_of_difficulty' 
                                OR disability_severity_oid_hearing = '2_lot_of_difficulty'
                                OR disability_severity_oid_mobility = '2_lot_of_difficulty'
                                OR disability_severity_oid_cognition = '2_lot_of_difficulty'
                                OR disability_severity_oid_selfcare = '2_lot_of_difficulty'
                                OR disability_severity_oid_communication = '2_lot_of_difficulty'
                            THEN 1 
                        ELSE 0 END lot_of_difficulty,
                        CASE 
                            WHEN disability_severity_oid_vision = '3_cannot_do' 
                                OR disability_severity_oid_hearing = '3_cannot_do'
                                OR disability_severity_oid_mobility = '3_cannot_do'
                                OR disability_severity_oid_cognition = '3_cannot_do'
                                OR disability_severity_oid_selfcare = '3_cannot_do'
                                OR disability_severity_oid_communication = '3_cannot_do'
                            THEN 1 
                        ELSE 0 END cannot_do
                    FROM learner l
                    LEFT JOIN person p ON p.uuid = l.person_uuid
                    WHERE l.deleted_at is null
                ) sd ON sd.person_uuid = pa.person_uuid
                WHERE pa.attendance_am_status_oid is not null OR pa.attendance_pm_status_oid is not null
                    AND pa.deleted_at is null
                GROUP BY pa.date
            ) AS subquery;
        ";
        return DB::select($sql);
    }

    public function absenteeismDistributionChart(){
        $sql = "
        SELECT
            MONTHNAME(date) month_name,
            MONTH(date) mont,
            date,
            COUNT(no_difficulty) count_no_difficulty,
            COUNT(some_difficulty) count_some_difficulty,
            COUNT(lot_of_difficulty) count_lot_of_difficulty,
            COUNT(cannot_do) count_cannot_do,
            COUNT(males) count_males,
            COUNT(females) count_females,
            COUNT(pregnant) count_pregnant,
            COUNT(mothers) count_mothers,
            COUNT(maternal_none) count_maternal_none
        FROM (
        SELECT 
                date,
                IF(date is not null,1,0) attendance_days,
                IF(no_difficulty > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) no_difficulty,
                IF(some_difficulty > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) some_difficulty,
                IF(lot_of_difficulty > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) lot_of_difficulty,
                IF(cannot_do > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) cannot_do,
                IF(males > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) males,
                IF(females > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) females,
                IF(mothers > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) mothers,
                IF(pregnant > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) pregnant,
                IF(maternal_none > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) maternal_none
            FROM person_attendance pa
            LEFT JOIN (
                SELECT 
                    l.person_uuid,
                    CASE 
                        WHEN p.sex_oid = 'male' THEN 1 
                    ELSE 0 END males,
                    CASE 
                        WHEN p.sex_oid = 'female' THEN 1 
                    ELSE 0 END females,
                    CASE 
                        WHEN l.maternal_status_oid = 'moth' THEN 1 
                    ELSE 0 END mothers,
                    CASE 
                        WHEN l.maternal_status_oid = 'preg' THEN 1 
                    ELSE 0 END pregnant,
                    CASE 
                        WHEN l.maternal_status_oid = 'none' THEN 1 
                    ELSE 0 END maternal_none,
                    CASE 
                        WHEN disability_severity_oid_vision = '0_no_difficulty' 
                            OR disability_severity_oid_hearing = '0_no_difficulty'
                            OR disability_severity_oid_mobility = '0_no_difficulty'
                            OR disability_severity_oid_cognition = '0_no_difficulty'
                            OR disability_severity_oid_selfcare = '0_no_difficulty'
                            OR disability_severity_oid_communication = '0_no_difficulty'
                        THEN 1 
                    ELSE 0 END no_difficulty,
                    CASE 
                        WHEN disability_severity_oid_vision = '1_some_difficulty' 
                            OR disability_severity_oid_hearing = '1_some_difficulty'
                            OR disability_severity_oid_mobility = '1_some_difficulty'
                            OR disability_severity_oid_cognition = '1_some_difficulty'
                            OR disability_severity_oid_selfcare = '1_some_difficulty'
                            OR disability_severity_oid_communication = '1_some_difficulty'
                        THEN 1 
                    ELSE 0 END some_difficulty,
                    CASE 
                        WHEN disability_severity_oid_vision = '2_lot_of_difficulty' 
                            OR disability_severity_oid_hearing = '2_lot_of_difficulty'
                            OR disability_severity_oid_mobility = '2_lot_of_difficulty'
                            OR disability_severity_oid_cognition = '2_lot_of_difficulty'
                            OR disability_severity_oid_selfcare = '2_lot_of_difficulty'
                            OR disability_severity_oid_communication = '2_lot_of_difficulty'
                        THEN 1 
                    ELSE 0 END lot_of_difficulty,
                    CASE 
                        WHEN disability_severity_oid_vision = '3_cannot_do' 
                            OR disability_severity_oid_hearing = '3_cannot_do'
                            OR disability_severity_oid_mobility = '3_cannot_do'
                            OR disability_severity_oid_cognition = '3_cannot_do'
                            OR disability_severity_oid_selfcare = '3_cannot_do'
                            OR disability_severity_oid_communication = '3_cannot_do'
                        THEN 1 
                    ELSE 0 END cannot_do
                FROM learner l
                LEFT JOIN person p ON p.uuid = l.person_uuid
                WHERE l.deleted_at is null
            ) sd ON sd.person_uuid = pa.person_uuid
            WHERE pa.attendance_am_status_oid is not null OR pa.attendance_pm_status_oid is not null
                AND pa.deleted_at is null
            ) subquery
            GROUP BY mont
            ORDER BY date
        ";
        return DB::select($sql);
    }


    public function absenteeismDistributionChartSecond(){
        $sql="
        SELECT 
            count_no_difficulty day_no_difficulty,
            count_males day_males,
            count_some_difficulty day_some_difficulty,
            count_lot_of_difficulty day_lot_of_difficulty,
            count_cannot_do day_cannot_do,
            count_females day_females,
            count_pregnant day_pregnant,
            count_mothers day_mothers,
            count_maternal_none day_maternal_none,
            COUNT(count_no_difficulty) count_no_difficulty,
            COUNT(count_some_difficulty) count_some_difficulty,
            COUNT(count_lot_of_difficulty) count_lot_of_difficulty,
            COUNT(count_cannot_do) count_cannot_do,
            COUNT(count_males) count_males,
            COUNT(count_females) count_females,
            COUNT(count_pregnant) count_pregnant,
            COUNT(count_mothers) count_mothers,
            COUNT(count_maternal_none) count_maternal_none
        FROM(
        SELECT
        date,
        person_uuid,
        IF(COUNT(no_difficulty) > 0, COUNT(no_difficulty),NULL) count_no_difficulty,
        IF(COUNT(some_difficulty) > 0, COUNT(some_difficulty),NULL)  count_some_difficulty,
        IF(COUNT(lot_of_difficulty) > 0, COUNT(lot_of_difficulty),NULL)  count_lot_of_difficulty,
        IF(COUNT(cannot_do) > 0, COUNT(cannot_do),NULL) count_cannot_do,
        IF(COUNT(males) > 0, COUNT(males),NULL) count_males,
        IF(COUNT(females) > 0, COUNT(females),NULL) count_females,
        IF(COUNT(pregnant) > 0, COUNT(pregnant),NULL) count_pregnant,
        IF(COUNT(mothers) > 0, COUNT(mothers),NULL) count_mothers,
        IF(COUNT(maternal_none) > 0, COUNT(maternal_none),NULL) count_maternal_none
        FROM (

        SELECT 
            date,
            pa.person_uuid,
            IF(date is not null,1,0) attendance_days,
            IF(no_difficulty > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) no_difficulty,
            IF(some_difficulty > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) some_difficulty,
            IF(lot_of_difficulty > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) lot_of_difficulty,
            IF(cannot_do > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) cannot_do,
            IF(males > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) males,
            IF(females > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) females,
            IF(mothers > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) mothers,
            IF(pregnant > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) pregnant,
            IF(maternal_none > 0 AND pa.attendance_am_status_oid ='absent' AND pa.attendance_pm_status_oid ='absent',1,null) maternal_none
        FROM person_attendance pa
        LEFT JOIN (
            SELECT 
                l.person_uuid,
                CASE 
                    WHEN p.sex_oid = 'male' THEN 1 
                ELSE 0 END males,
                CASE 
                    WHEN p.sex_oid = 'female' THEN 1 
                ELSE 0 END females,
                CASE 
                    WHEN l.maternal_status_oid = 'moth' THEN 1 
                ELSE 0 END mothers,
                CASE 
                    WHEN l.maternal_status_oid = 'preg' THEN 1 
                ELSE 0 END pregnant,
                CASE 
                    WHEN l.maternal_status_oid = 'none' THEN 1 
                ELSE 0 END maternal_none,
                CASE 
                    WHEN disability_severity_oid_vision = '0_no_difficulty' 
                        OR disability_severity_oid_hearing = '0_no_difficulty'
                        OR disability_severity_oid_mobility = '0_no_difficulty'
                        OR disability_severity_oid_cognition = '0_no_difficulty'
                        OR disability_severity_oid_selfcare = '0_no_difficulty'
                        OR disability_severity_oid_communication = '0_no_difficulty'
                    THEN 1 
                ELSE 0 END no_difficulty,
                CASE 
                    WHEN disability_severity_oid_vision = '1_some_difficulty' 
                        OR disability_severity_oid_hearing = '1_some_difficulty'
                        OR disability_severity_oid_mobility = '1_some_difficulty'
                        OR disability_severity_oid_cognition = '1_some_difficulty'
                        OR disability_severity_oid_selfcare = '1_some_difficulty'
                        OR disability_severity_oid_communication = '1_some_difficulty'
                    THEN 1 
                ELSE 0 END some_difficulty,
                CASE 
                    WHEN disability_severity_oid_vision = '2_lot_of_difficulty' 
                        OR disability_severity_oid_hearing = '2_lot_of_difficulty'
                        OR disability_severity_oid_mobility = '2_lot_of_difficulty'
                        OR disability_severity_oid_cognition = '2_lot_of_difficulty'
                        OR disability_severity_oid_selfcare = '2_lot_of_difficulty'
                        OR disability_severity_oid_communication = '2_lot_of_difficulty'
                    THEN 1 
                ELSE 0 END lot_of_difficulty,
                CASE 
                    WHEN disability_severity_oid_vision = '3_cannot_do' 
                        OR disability_severity_oid_hearing = '3_cannot_do'
                        OR disability_severity_oid_mobility = '3_cannot_do'
                        OR disability_severity_oid_cognition = '3_cannot_do'
                        OR disability_severity_oid_selfcare = '3_cannot_do'
                        OR disability_severity_oid_communication = '3_cannot_do'
                    THEN 1 
                ELSE 0 END cannot_do
            FROM learner l
            LEFT JOIN person p ON p.uuid = l.person_uuid
            WHERE l.deleted_at is null
        ) sd ON sd.person_uuid = pa.person_uuid
        WHERE pa.attendance_am_status_oid is not null OR pa.attendance_pm_status_oid is not null
            AND pa.deleted_at is null
            
        ) subquery
        GROUP BY person_uuid


        ) mainsub
        GROUP BY count_no_difficulty,
        count_males,
        count_females,
        count_some_difficulty,
        count_lot_of_difficulty,
        count_cannot_do,
        count_pregnant,
        count_mothers,
        count_maternal_none

        
        ";
        return DB::select($sql);
    }

    public function countLearnerAttendanceDays(){
        $sql="
        SELECT 
            pa.date,
            ROW_NUMBER() OVER (ORDER BY pa.date) row_num
        FROM person_attendance pa
        WHERE attendance_am_status_oid is not null 
            AND attendance_pm_status_oid is not null
            AND deleted_at is null
        GROUP BY date
        ";
        return DB::select($sql);
    }

    public function absenteeismDistribution(){
        $sql ="
        SELECT 
            date,
            COUNT(date) count_learners
        FROM 
        person_attendance pa 
        WHERE pa.attendance_am_status_oid ='absent' 
            AND pa.attendance_pm_status_oid = 'absent'
            AND pa.deleted_at is null 
        GROUP BY date
        ORDER BY date
        ";
        return DB::select($sql);
    }

    public function absenteeismDistributionChartAnalysis(){
        // $attendanceDays = collect($this->countLearnerAttendanceDays());
        // $absenteeismDistributionData = $this->absenteeismDistribution();
        $learnerAttendanceData = collect($this->absenteeismDistributionChartSecond());
        // $totalDaysCount = $attendanceDays->count();

       
        $learnerCollectData = collect($learnerAttendanceData);

       
        $noDifficultyArray = $learnerCollectData->groupBy('day_no_difficulty')->map(function ($row) {
            return $row->sum('count_no_difficulty');
        });
        $someDifficultyArray = $learnerCollectData->groupBy('day_some_difficulty')->map(function ($row) {
            return $row->sum('count_some_difficulty');
        });
        $alotDifficultyArray = $learnerCollectData->groupBy('day_lot_of_difficulty')->map(function ($row) {
            return $row->sum('count_lot_of_difficulty');
        });
        $cannotDifficultyArray = $learnerCollectData->groupBy('day_cannot_do')->map(function ($row) {
            return $row->sum('count_cannot_do');
        });
        $maleDayArray = $learnerCollectData->groupBy('day_males')->map(function ($row) {
            return $row->sum('count_males');
        });
        $femaleDayArray= $learnerCollectData->groupBy('day_females')->map(function ($row) {
            return $row->sum('count_females');
        });
        $pregnantDayArray= $learnerCollectData->groupBy('day_pregnant')->map(function ($row) {
            return $row->sum('count_pregnant');
        });
        $motherDayArray= $learnerCollectData->groupBy('day_mothers')->map(function ($row) {
            return $row->sum('count_mothers');
        });
        $maternalDayArray= $learnerCollectData->groupBy('day_maternal_none')->map(function ($row) {
            return $row->sum('count_maternal_none');
        });

        $noDifficultyArray->forget("");
        $someDifficultyArray->forget("");
        $alotDifficultyArray->forget("");
        $cannotDifficultyArray->forget("");
        $maleDayArray->forget("");
        $femaleDayArray->forget("");
        $pregnantDayArray->forget("");
        $motherDayArray->forget("");
        $maternalDayArray->forget("");

        $distributionData = [
            "no_difficulty" => $noDifficultyArray,
            "some_difficulty" => $someDifficultyArray,
            "alot_difficulty" => $alotDifficultyArray,
            "cannot_difficulty" => $cannotDifficultyArray,
            "males" => $maleDayArray,
            "females" => $femaleDayArray,
            "pregnant" => $pregnantDayArray,
            "mother" => $motherDayArray,
            "maternal_none" => $maternalDayArray
        ];
        
        $absenteeismDaysFromConditions = [
            $noDifficultyArray->count(),
            $someDifficultyArray->count(),
            $cannotDifficultyArray->count(),
            $maleDayArray->count(),
            $femaleDayArray->count(),
            $motherDayArray->count(),
            $maternalDayArray->count()
        ];
        // foreach ($attendanceDays as $day) {
        //     $percent = round($day->row_num / $totalDaysCount * 100,0);
        // }

        return ["learnerData"=>$distributionData,"maxAttendanceDay"=>max($absenteeismDaysFromConditions)];
    }

    public function trendsOverTimeChart(){
        $sql = "
        SELECT 
            month_name,
            month_number,
            date,
            COUNT(serverly_absent) count_serverly_absent,
            COUNT(persistent_absent) count_persistent_absent,
            COUNT(at_risk) count_at_risk
        FROM(
            SELECT 
                MONTHNAME(subquery.date) month_name,
                MONTH(subquery.date) month_number,
                COUNT(subquery.date) days,
                subquery.date,
                absent_query.absent_days,
                ROUND(absent_query.absent_days/COUNT(subquery.date)*100) rate,
                CASE 
                    WHEN ROUND(absent_query.absent_days/COUNT(subquery.date)*100) >= 50 THEN 1 
                ELSE null END serverly_absent,
                CASE 
                    WHEN ROUND(absent_query.absent_days/COUNT(subquery.date)*100) BETWEEN 10 AND 49 THEN 1 
                ELSE null END persistent_absent,
                CASE 
                    WHEN ROUND(absent_query.absent_days/COUNT(subquery.date)*100) < 10 THEN 1 
                ELSE null END at_risk
            FROM
            (	
                SELECT date FROM person_attendance 
                WHERE attendance_am_status_oid is not null 
                AND attendance_pm_status_oid is not null 
                AND deleted_at is null
                GROUP BY date
            )subquery
            LEFT JOIN (
                SELECT
                    MONTH(pa.date) month_no, 
                    pa.date,
                    pa.person_uuid ,
                    COUNT(pa.person_uuid) absent_days
                FROM person_attendance pa 
                WHERE pa.entity_type_oid = 'learner'
                    AND pa.attendance_am_status_oid = 'absent' AND pa.attendance_pm_status_oid = 'absent' 
                    AND deleted_at is NULL 
                GROUP BY month_no, pa.person_uuid 
            ) absent_query ON MONTH(subquery.date) = absent_query.month_no
            GROUP BY month_number,absent_query.person_uuid
        )m_sub
        GROUP BY month_number
        ORDER BY date
        ";
        return DB::select($sql);
    }
}
