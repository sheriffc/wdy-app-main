<?php

namespace App\Http\Controllers;

use App\Common\Queries\DbQueries;
use App\Common\Utils;
use App\Queries\Landing;
use Illuminate\Support\Facades\DB;
use App\Queries\ProfileCompletion;
use App\Queries\School;
use App\Queries\SchoolAttendanceMonitoring;
use App\Queries\TeacherReports;
use App\Queries\LearnerReports;
use App\Queries\SchoolFeeding;
use App\Queries\TeacherProfile;
use App\Queries\LearnerProfile;
use App\Queries\LearnerPerformanceQueries;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PublicController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function getActiveDistricts(){
        Utils::successResponse("success",DbQueries::getActiveDistricts());
    }

    public function getAcademicYearsForFilter(){
        $years = DB::table('school_academic_year')
            ->whereNull('deleted_at')
            ->orderByDesc('academic_year')
            ->get(['uuid', 'academic_year', 'academic_year_name', 'date_from', 'date_to', 'active']);
        Utils::successResponse("success", $years);
    }

    public function getDistrictChiefdoms(Request $request){
        Utils::successResponse("success",DbQueries::getDistrictChiefdoms($request->districtId));
    }

    public function landing(Landing $landingQueries)
    {
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        return view('landing',['isDistrictOfficerOrAbove' => $isDistrictOfficerOrAbove]);
    }

    public function getLandingDropdownDates(Landing $landingQueries, Request $request){

        $selectedDate = ($request->has(['selectedDate']) && !empty($request->selectedDate)) ?
            $request->selectedDate : $landingQueries->getMaxDateTeacherAttendance()->max_attendance_date;

        Utils::successResponse("success", $landingQueries->getDropdownDatesTeacherAttendance( min(Carbon::now()->toDateString(),$selectedDate)));
    }

    public function getLandingData(Landing $landingQueries, Request $request){
        $todayDate =  Carbon::now()->toDateString();

        $queryResultMaxTeacherAttendanceDate = $landingQueries->getMaxDateTeacherAttendance();

        $maxTeacherAttendanceDate = (!empty($queryResultMaxTeacherAttendanceDate->max_attendance_date)) ?
            $queryResultMaxTeacherAttendanceDate->max_attendance_date : $todayDate;

        $maxTeacherAttendanceDate = min($todayDate, $maxTeacherAttendanceDate);
        
        $endDate = ($request->has(['endDate']) && !empty($request->endDate)) ?
            $request->endDate : $maxTeacherAttendanceDate;

        $startDate = ($request->has(['startDate']) && !empty($request->startDate)) ?
            $request->startDate : Carbon::parse($maxTeacherAttendanceDate)->subDays(6)->toDateString();

        $academicYear = ($request->has('academicYear') && !empty($request->academicYear)) ? intval($request->academicYear) : null;

        $schoolCollectionData = collect($landingQueries->getSchoolsAttendanceChart($endDate, $request->districtId, $academicYear));

        $todayHeadlineTotals =[
                "schools_total"=> $schoolCollectionData->count(),
                "schools_reported"=> $schoolCollectionData->where('teachers_reported','>',0)->count(),
                "teachers_total"=> $schoolCollectionData->sum('teachers_total'),
                "learners_total"=> $schoolCollectionData->sum('learners_total'),
                "teachers_reported"=> $schoolCollectionData->sum('teachers_reported'),
                "teachers_present"=> $schoolCollectionData->sum('teachers_present'),
                "teachers_absent"=> $schoolCollectionData->sum('teachers_absent'),
                "learners_reported"=> $schoolCollectionData->sum('learners_reported'),
        ];
        
        $attendanceMapArray = array();
        $schoolCollectionData->where('lat','>',0)
            ->each(function ($item) use(&$attendanceMapArray){
                array_push($attendanceMapArray,$item);
            });

        Utils::successResponse("success",[
            "teacherAttendanceBarchart" => $landingQueries->getTeacherAttendanceBarchart($startDate,$endDate,$request->districtId),
            "todayHeadlineTotals" => $todayHeadlineTotals,
            "attendanceMap" => $attendanceMapArray,
            "date" => Carbon::parse($endDate)->isoFormat("dddd, Do MMM YYYY")
        ]);
    }

    public function getclassGenderRatio(Landing $landingQueries, Request $request){
        $academicYear = ($request->has('academicYear') && !empty($request->academicYear)) ? intval($request->academicYear) : null;
        $classGenderRatioCollection = collect($landingQueries->classGenderRatio($request->districtId, $academicYear));

        $femaleArray = $classGenderRatioCollection->where('sex','=','female')->pluck('count');
        $maleArray = $classGenderRatioCollection->where('sex','=','male')->pluck('count');

        $femaleTotal = $femaleArray->sum();
        $maleTotal = $maleArray->sum();

        $totalLearners = $maleTotal + $femaleTotal;

        $classCategories = $classGenderRatioCollection->unique('class_level');


        Utils::successResponse("success",[
            "classEnrolmentByGender" => [
                "females"=> $femaleArray,
                "males"=> $maleArray,
            ],
            "classCategories" => $classCategories->pluck('class_level'),
            "genderRatio" => [
                "male"=>$maleTotal,
                "female"=> $femaleTotal,
                "totalLearners" => $totalLearners
                ]
        ]);
    }

    public function getMaternalStatusChart(Landing $landingQueries,Request $request){
        Utils::successResponse("success",$landingQueries->getSchoolsMaternalStatusChart($request->districtId));
    }

    public function getMaternalStatusGeoLocation(Landing $landingQueries,Request $request){
        Utils::successResponse("success",["maternalStatusGeoLocation"=> $landingQueries->getMaternalStatusGeoMap($request->districtId)]);
    }

    public function getLearnerAbsenteeismRates(Landing $landingQueries,Request $request){
        Utils::successResponse("success",["absenteeismRates"=> $landingQueries->getLearnerAbsenteeismRates($request->districtId)]);
    }

    public function getSpecialNeedsChart(Landing $landingQueries,Request $request){
        Utils::successResponse("success",$landingQueries->getLearnerWithSpecialNeedsChart($request->districtId));
    }

    public function getSpecialNeedsAttendanceAbsenteeismChart(Landing $landingQueries,Request $request){
        Utils::successResponse("success",$landingQueries->getSpecialNeedsAttendanceAbsenteeismData($request->districtId));
    }

    public function getRemovedLearnersChart(Landing $landingQueries,Request $request){
        Utils::successResponse("success",$landingQueries->removedLearnersChart($request->districtId));
    }

    public function geAtRiskLearnersChart(Landing $landingQueries,Request $request){
        Utils::successResponse("success",$landingQueries->getAtRiskLearnersChart($request->districtId));
    }

    public function geAtRiskSchoolsChart(Landing $landingQueries,Request $request){
        Utils::successResponse("success",$landingQueries->getAtRiskSchoolLearnerChart($request->districtId));
    }

    public function getGenderSeverityChart(Landing $landingQueries,Request $request){
        Utils::successResponse("success",$landingQueries->getGenderSeverityAbseenteismRate());
    }

    public function getDistrictAbsenteeismChart(Landing $landingQueries,Request $request){
        Utils::successResponse("success",$landingQueries->districtAbseenteeismRate());
    }

    public function getAgeAbsenteeismChart(Landing $landingQueries,Request $request){
        Utils::successResponse("success",$landingQueries->ageAbseenteeismRate());
    }

    public function getAbsenteeismDistributionChart(Landing $landingQueries,Request $request){
        // Utils::successResponse("success",$landingQueries->absenteeismDistributionChart());
        Utils::successResponse("success",$landingQueries->absenteeismDistributionChartAnalysis());
    }

    public function getTrendsOverTimeChart(Landing $landingQueries,Request $request){
        Utils::successResponse("success",$landingQueries->trendsOverTimeChart());
    }

    public function schoolList(){

        $currentDate = Carbon::now()->isoFormat("dddd, Do MMM YYYY");

        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }

        return view('school.school-list',[
            'currentDate' => $currentDate,
            'isDistrictOfficerOrAbove' => $isDistrictOfficerOrAbove
        ]);
    }

    public function schoolProfile($uuid, School $schoolQueries, SchoolFeeding $schoolFeedingQueries){

        $isDistrictOfficerOrAbove = Auth::check() && Auth::user()->user_type_id >= 40;
        $isSchoolLeaderOfThisSchool = $this->isSchoolLeaderOf($uuid);

        return view('school.school-profile',[
            "schoolInfo"                 => $schoolQueries->getSchoolDetails($uuid),
            "feedingRecords"             => $schoolFeedingQueries->getBySchool($uuid),
            "stockRecords"               => $schoolFeedingQueries->getStockBySchool($uuid),
            'isDistrictOfficerOrAbove'   => $isDistrictOfficerOrAbove,
            'isSchoolLeaderOfThisSchool' => $isSchoolLeaderOfThisSchool,
        ]);
    }

    public function getSchoolProfileDropdownDates($uuid, School $schoolQueries, Request $request){

        $selectedDate = ($request->has(['selectedDate']) && !empty($request->selectedDate)) ?
            $request->selectedDate : $schoolQueries->getMaxDateTeacherAttendance($uuid)->max_attendance_date;

        Utils::successResponse("success",$schoolQueries->getDropdownDatesTeacherAttendance($uuid,min(Carbon::now()->toDateString(),$selectedDate)));
    }

    public function getSchoolProfileData($uuid, School $schoolQueries, Request $request){

        $todayDate = Carbon::now()->toDateString();

        $queryResultMaxTeacherAttendanceDate = $schoolQueries->getMaxDateTeacherAttendance($uuid);

        $maxTeacherAttendanceDate = (!empty($queryResultMaxTeacherAttendanceDate->max_attendance_date)) ?
            $queryResultMaxTeacherAttendanceDate->max_attendance_date : $todayDate;

        $maxTeacherAttendanceDate = min($todayDate,$maxTeacherAttendanceDate);
        
        $endDate = ($request->has(['endDate']) && !empty($request->endDate)) ? $request->endDate : $maxTeacherAttendanceDate;

        $startDate = ($request->has(['startDate']) && !empty($request->startDate)) ?
            $request->startDate : Carbon::parse($maxTeacherAttendanceDate)->subDays(6)->toDateString();

        $isDistrictOfficerOrAbove = Auth::check() && Auth::user()->user_type_id >= 40;
        $isSchoolLeaderOfThisSchool = $this->isSchoolLeaderOf($uuid);
        $canSeePrivateData = $isDistrictOfficerOrAbove || $isSchoolLeaderOfThisSchool;

        $teachersRemovedFromPayroll = [];
        if ($canSeePrivateData) {
            $teachersRemovedFromPayroll = $schoolQueries->getTeachersRemovedFromPayroll($uuid);
        }

        Utils::successResponse("success",[
            "teacherTable"              => $schoolQueries->getTeacherTable($uuid, $endDate, $canSeePrivateData),
            "learnerTable"              => $schoolQueries->getLearnerTable($uuid, $todayDate, $canSeePrivateData),
            "teacherAttendanceBarchart" => $schoolQueries->getTeacherAttendanceBarchart($uuid, $startDate, $endDate),
            "classroomTable"            => $schoolQueries->getClassroomTable($uuid, $todayDate),
            "teachersRemovedFromPayroll"=> $teachersRemovedFromPayroll,
            "date"                      => Carbon::parse($endDate)->isoFormat("dddd, Do MMM YYYY")
        ]);
    }

    public function reportMonitoring()
    {
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        return view('report-monitoring',['isDistrictOfficerOrAbove' => $isDistrictOfficerOrAbove]);
    }

    public function getSchoolDailyReportChartData(Landing $landingQueries, Request $request){
        $todayDate = Carbon::now()->toDateString();
        $queryResultMaxTeacherAttendanceDate = $landingQueries->getMaxDateTeacherAttendance();

        $maxTeacherAttendanceDate = (!empty($queryResultMaxTeacherAttendanceDate->max_attendance_date)) ?
            $queryResultMaxTeacherAttendanceDate->max_attendance_date : $todayDate;

        $maxTeacherAttendanceDate = min($todayDate,$maxTeacherAttendanceDate);

        $endDate = ($request->has(['selectedDate']) && !empty($request->selectedDate)) ?
            $request->selectedDate : $maxTeacherAttendanceDate;
        $schoolData = $landingQueries->getSchoolsAttendanceChart($endDate, $request->districtId);
        $collection = collect($schoolData);
        $schoolsTeachersReportedCardData=[
            "schools_total"=>$collection->count(),
            "schools_reported"=> $collection->where("teachers_reported",">",0)->count(),
        ];
        $schoolsLearnersReportedCardData=[
            "schools_total"=>$collection->count(),
            "schools_reported"=> $collection->where("learners_reported",">",0)->count(),
        ];
        Utils::successResponse("success",[
            "attendanceMapData" => $schoolData,
            "schoolsTeachersReportedCardData"=> $schoolsTeachersReportedCardData,
            "schoolsLearnersReportedCardData"=> $schoolsLearnersReportedCardData,
            "date" => Carbon::parse($endDate)->isoFormat("dddd, Do MMM YYYY")
        ]);
    }

    public function schoolAttendanceMonitoring(){

        $currentDate = Carbon::now()->isoFormat("dddd, Do MMM YYYY");

        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }

        return view('school.shool-attendance-monitoring',[
            'currentDate' => $currentDate,
            'isDistrictOfficerOrAbove' => $isDistrictOfficerOrAbove
        ]);
    }

    public function getSchoolAttendanceMonitoringData(SchoolAttendanceMonitoring $attendanceMonitoringQuery, Request $request){
       
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }

        $endDate = ($request->has(['selectedDate']) && !empty($request->selectedDate)) ?
            $request->selectedDate :  Carbon::now()->toDateString();
     
        $getWeeklySchoolAttendanceMonitoring = $attendanceMonitoringQuery->getWeeklySchoolAttendanceMonitoring($endDate,$request->districtId,$isDistrictOfficerOrAbove);
        Utils::successResponse("success",[
            "schoolAttendanceMonitoringData"=> $getWeeklySchoolAttendanceMonitoring['schoolAttendanceMonitoringData'],
            "attendanceMissingTable" => $getWeeklySchoolAttendanceMonitoring['attendanceMissingTable'],
            "date" => Carbon::parse($endDate)->isoFormat("ddd, Do MMM YYYY")
        ]);
    }

    public function getSchoolProfileCompletionTable(ProfileCompletion $profileCompletion, Request $request){
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }

        Utils::successResponse("success",
            $profileCompletion->schoolProfileCompletionCharts($request->districtId,$isDistrictOfficerOrAbove)
        );
    }

    public function getSchoolLearnersProfileCompletionTable(ProfileCompletion $profileCompletion, Request $request){
        Utils::successResponse("success",[
            "schoolLearnersProfileCompletionTable" => $profileCompletion->schoolLearnersProfileCompletionTable($request->districtId),
        ]);
    }

    public function teacherReports(){

        $currentDate = Carbon::now()->isoFormat("dddd, Do MMM YYYY");

        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }

        return view('teacher-reports',[
            'currentDate' => $currentDate,
            'isDistrictOfficerOrAbove' => $isDistrictOfficerOrAbove
        ]);
    }

    public function getTeachersEligbleForSanctionsTable(TeacherReports $teacherReports, Request $request){

        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        
        Utils::successResponse("success",[
            "teachersEligbleForSanctionsTable" => $teacherReports->teachersEligbleForSanctionsTable($request->districtId,$request->chiefdomId,$request->month,$isDistrictOfficerOrAbove)
        ]);
    }

    public function getUnauthorisedTeacherTransfersTable(TeacherReports $teacherReports, Request $request){
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        Utils::successResponse("success",[
            "unauthorisedTeacherTransfersTable" => $teacherReports->unauthorisedTeacherTransfersTable($request->districtId,$request->chiefdomId,$isDistrictOfficerOrAbove)
        ]);
    }

    public function getRemovableTeachersTable(TeacherReports $teacherReports, Request $request){
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        Utils::successResponse("success",[
            "removableTeachersTable" => $teacherReports->removableTeachersTable($request->districtId,$request->chiefdomId,$isDistrictOfficerOrAbove)
        ]);
    }
    
    public function getTeacherRequiringInvestigationTable(TeacherReports $teacherReports, Request $request){
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        Utils::successResponse("success",[
            "teacherRequiringInvestigationTable" => $teacherReports->teacherRequiringInvestigationTable($request->districtId,$request->chiefdomId,$isDistrictOfficerOrAbove)
        ]);
    }

    public function getActiveTeachersTable(TeacherReports $teacherReports, Request $request){
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        Utils::successResponse("success",[
            "activeTeachersTable" => $teacherReports->activeTeachersTable($request->districtId,$request->chiefdomId,$isDistrictOfficerOrAbove)
        ]);
    }

    public function learnerReports(){

        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }

        $ay = DB::selectOne('SELECT date_from, date_to FROM school_academic_year WHERE active = 1 LIMIT 1');

        return view('learner-reports',[
            'isDistrictOfficerOrAbove' => $isDistrictOfficerOrAbove,
            'ayDateFrom' => $ay->date_from ?? null,
            'ayDateTo'   => $ay->date_to   ?? null,
        ]);
    }

    public function getUnassignedLearnersTable(LearnerReports $learnerReports, Request $request){
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        Utils::successResponse("success",[
            "unassignedLearnersTable" => $learnerReports->unassignedLearnersTable($request->districtId,$isDistrictOfficerOrAbove)
        ]);
    }

    public function getDisabilityLearnersTable(LearnerReports $learnerReports, Request $request){
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        Utils::successResponse("success",[
            "disabilityLearnersTable" => $learnerReports->learnerDisabilityTable($request->districtId,$isDistrictOfficerOrAbove)
        ]);
    }

    public function getAtRiskLearnersTable(LearnerReports $learnerReports, Request $request){
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        Utils::successResponse("success",[
            "atRiskLearnersTable" => $learnerReports->getAtRiskLearnersChart(
                $request->districtId,
                $isDistrictOfficerOrAbove,
                $request->month,
                $request->year
            )
        ]);
    }

    public function getDuplicateEnrollmentTable(LearnerReports $learnerReports, Request $request){
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        Utils::successResponse("success",[
            "duplicateEnrollmentTable" => $learnerReports->duplicateEnrollmentTable($request->districtId, $isDistrictOfficerOrAbove)
        ]);
    }

    public function teacherProfile($uuid,TeacherProfile $teacherProfile, Request $request){

        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }

        $teacherProfileDetails = $teacherProfile->teacherDetails($uuid);

        $url="";
        if(isset($teacherProfileDetails->media_created_at)){
            try {
                $androidBuildType = env("ANDROID_BUILD_TYPE");
                $dateArray = explode("-",$teacherProfileDetails->media_created_at);
                $path= "mobile/{$androidBuildType}/teacher_photos/{$dateArray[0]}/{$teacherProfileDetails->media_created_at}/{$teacherProfileDetails->uuid}.jpg";
                $url = Storage::disk('s3')->temporaryURL($path, now()->addMinutes(5));
            } catch (\Exception $e) {
                // S3 not configured locally — portrait is served from DB base64 instead
            }
        }

        return view('teacher-profile',[
            'isDistrictOfficerOrAbove' => $isDistrictOfficerOrAbove,
            'teacherProfileDetails'=> $teacherProfileDetails,
            'potraitPhotoUrl'=> $url
        ]);
    }

    public function getTeacherSchoolData($uuid,TeacherProfile $teacherProfile, Request $request){
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        Utils::successResponse("success",$teacherProfile->getTeacherSchoolData($uuid,$isDistrictOfficerOrAbove));
    }

    public function getTeacherAssignedLearners($uuid,TeacherProfile $teacherProfile, Request $request){
        $isDistrictOfficerOrAbove = false;

        if( Auth::check() && Auth::user()->user_type_id >= 40){
            $isDistrictOfficerOrAbove = true;
        }
        Utils::successResponse("success",$teacherProfile->getTeacherLearners($uuid,$isDistrictOfficerOrAbove));
    }

    public function getTeacherAttendanceSummaryData($uuid,TeacherProfile $teacherProfile, Request $request){
        Utils::successResponse("success",$teacherProfile->getTeacherAttendanceSummary($uuid));
    }

    public function getTeacherAttendanceRecordsData($uuid,TeacherProfile $teacherProfile, Request $request){
        Utils::successResponse("success",$teacherProfile->getTeacherAttendanceRecords($uuid,$request->startDate,$request->endDate));
    }

    public function getTeacherTimeTableData($uuid,TeacherProfile $teacherProfile, Request $request){
        Utils::successResponse("success",$teacherProfile->getTeacherTimeTable($uuid));
    }

    public function getTeacherAbsentReasons(TeacherProfile $teacherProfile, Request $request){
        Utils::successResponse("success",$teacherProfile->getTeacherAbsentReasons());
    }

    public function learnerProfile($uuid, LearnerProfile $learnerProfile) {
        $isDistrictOfficerOrAbove = Auth::check() && Auth::user()->user_type_id >= 40;
        $details = $learnerProfile->learnerDetails($uuid);

        $isSchoolLeaderWithAccess = !$isDistrictOfficerOrAbove
            && $details
            && !empty($details->school_uuid)
            && $this->isSchoolLeaderOf($details->school_uuid);

        $isDistrictOfficerOrAbove = $isDistrictOfficerOrAbove || $isSchoolLeaderWithAccess;
        $schoolHistory = $learnerProfile->schoolHistory($uuid);
        $attendanceSummary = $learnerProfile->attendanceSummary($uuid);

        // Pivot performance records by academic_year → subject, with each term inline
        $reportCard = [];
        foreach ($learnerProfile->performanceRecords($uuid) as $row) {
            $year = $row->academic_year;
            $subj = $row->subject_oid;
            if (!isset($reportCard[$year])) {
                $reportCard[$year] = [
                    'class_label' => trim(($row->school_group_level ?? '') . ' ' . ($row->school_group_name ?? '')),
                    'subjects'    => [],
                ];
            }
            if (!isset($reportCard[$year]['subjects'][$subj])) {
                $reportCard[$year]['subjects'][$subj] = [
                    'subject_name' => $row->subject_name,
                    'order'        => $row->subject_order,
                    'first_term'   => null,
                    'second_term'  => null,
                    'third_term'   => null,
                ];
            }
            $reportCard[$year]['subjects'][$subj][$row->term_oid] = $row;
        }
        krsort($reportCard);
        foreach ($reportCard as &$yearData) {
            uasort($yearData['subjects'], fn($a, $b) => $a['order'] <=> $b['order']);
        }
        unset($yearData);

        return view('learner-profile', [
            'isDistrictOfficerOrAbove' => $isDistrictOfficerOrAbove,
            'details'          => $details,
            'schoolHistory'    => $schoolHistory,
            'attendanceSummary'=> $attendanceSummary,
            'reportCard'       => $reportCard,
            'learnerUuid'      => $uuid,
        ]);
    }

    public function getLearnerAttendanceRecords($uuid, LearnerProfile $learnerProfile, Request $request) {
        $startDate = $request->startDate ?? Carbon::now()->subDays(30)->toDateString();
        $endDate   = $request->endDate   ?? Carbon::now()->toDateString();
        Utils::successResponse("success", $learnerProfile->attendanceRecords($uuid, $startDate, $endDate));
    }

    public function schoolFeedingReport()
    {
        return view('school-feeding');
    }

    public function getSchoolFeedingTable(SchoolFeeding $schoolFeeding, Request $request)
    {
        Utils::successResponse("success", [
            "feedingTable" => $schoolFeeding->feedingTable($request->districtId),
        ]);
    }

    public function schoolsReport()
    {
        return view('schools-report');
    }

    public function getSchoolsTable(School $school, Request $request)
    {
        $rows = array_map(function ($row) {
            $row->wash               = School::washLabel($row->wash_oids);
            $row->classrooms         = School::classroomsLabel($row->classrooms_oid);
            $row->electricity        = School::electricityLabel($row->electricity_oids);
            $row->mno                = School::mnoLabel($row->mno_oids);
            $row->learning_materials = School::learningMaterialsLabel($row->learning_materials_oids);
            return $row;
        }, $school->schoolsTable($request->districtId));

        return Utils::successResponse("success", ["rows" => $rows]);
    }

    public function schoolFeedingSecretariat()
    {
        return view('school-feeding-secretariat');
    }

    public function getLearnerPerformanceDashboard(LearnerPerformanceQueries $q, Request $request) {
        $districtId  = $request->districtId  ?: null;
        $termOid     = $request->termOid     ?: null;
        $levelLabel  = $request->levelLabel  ?: null;

        return Utils::successResponse("success", [
            "summary"  => $q->summary($districtId, $termOid, $levelLabel),
            "trends"   => $q->trends($districtId, $termOid, $levelLabel),
            "subjects" => $q->subjects($districtId, $termOid, $levelLabel),
        ]);
    }

    public function getSchoolFeedingSecretariatTable(SchoolFeeding $schoolFeeding, Request $request)
    {
        return Utils::successResponse("success", [
            "rows" => $schoolFeeding->secretariatTable($request->districtId),
        ]);
    }

    private function isSchoolLeaderOf(string $schoolUuid): bool
    {
        if (!Auth::check() || Auth::user()->user_type_id != 20) {
            return false;
        }
        return DB::table('user_scope_custom_assignment')
            ->where('user_id', Auth::id())
            ->where('school_uuid', $schoolUuid)
            ->exists();
    }

}
