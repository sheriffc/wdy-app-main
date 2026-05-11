<?php

namespace App\Console\Commands;

use App\Api\DataSync;
use App\Api\DataSyncQueries;
use App\Common\Utils;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class LoadTesting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loadtesting:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script to populate main tables with x records';

    /**
     * Execute the console command.
     *
     * @return int
     */
    private array $districtOffices = [];
    private array $schools = [];
    private array $school = [];
    private array $academicYear = [];

    private array $schoolGroups = [];
    private array $teachers = [];
    private array $persons = [];
    private array $mediaPhotos = [];
    private array $personFingerprints = [];
    private array $personAttendance = [];
    private array $teacherTimetable = [];
    private array $learners = [];
    private array $learnerAdmissions = [];
    private array $learnerEnrolments = [];

    private string $examplePhotoData = '';
    private string $exampleFingerprintData = '';
    private array $exampleFirstnames = [];
    private array $exampleMiddlenames = [];
    private array $exampleLastnames = [];

    public function handle()
    {
        //check that this is a 'load testing database'
        $dbName = getenv("DB_DATABASE");
        if(empty($dbName)) return $this->error("ERROR: no database set");
        if(!str_contains($dbName, '_load')) return $this->error("ERROR: make sure you are using the load testing database, should have '_load' in database name");

        $beginTime = microtime(true);

        //schools
        $schoolsDefault = 100;
        $schoolsTC = $this->ask("number of schools (default is $schoolsDefault)");
        $schoolsTC = $this->checkInputIsValidInteger($schoolsTC, $schoolsDefault);

        //attendance
        $attendanceDefault = 60;
        $attendanceMax = 365;
        $attendanceTC = $this->ask("enter number of days attendance (default is $attendanceDefault, max is 365)");
        $attendanceTC = $this->checkInputIsValidInteger($attendanceTC, $attendanceDefault);
        if($attendanceTC > $attendanceMax) $attendanceTC = $attendanceMax;


//        //district offices
//        $districtOfficesDefault = 30;
//        $districtOfficesTC = $this->ask("number of district offices (default is $districtOfficesDefault)");
//        $districtOfficesTC = $this->checkInputIsValidInteger($districtOfficesTC, $districtOfficesDefault);
//        for ($i = 1; $i <= $districtOfficesTC; $i++) {
//            $this->districtOffices[] = [
//                'uuid' => Str::orderedUuid(),
//                'name' => "District Office $i",
//                'active'=>1,
//                'updated_at'=>Utils::dateTimeStamp(),
//                'created_at'=>Utils::dateTimeStamp(),
//                "synced_at"=>Utils::dateTimeStamp(),
//            ];
//        }

        //school academic year
        $academicYearDefault = 2023;
        $academicYearInt = $this->ask("enter the academic year (default is $academicYearDefault)");
        $academicYearInt = $this->checkInputIsValidInteger($academicYearInt, $academicYearDefault);

        $this->academicYear = [
            'uuid' => Str::orderedUuid(),
            "academic_year_name"=>"academic year $academicYearInt",
            "academic_year"=>$academicYearInt,
            "date_from"=>date('Y-m-d', strtotime("1-1-".$academicYearInt)),
            "date_to"=>date('Y-m-d', strtotime("1-12-".$academicYearInt)),
            "active"=>1,
            "created_at"=>Utils::dateTimeStamp(),
            "updated_at"=>Utils::dateTimeStamp(),
            "synced_at"=>Utils::dateTimeStamp(),
        ];

        //school groups
        $schoolGroupsInput = $this->ask("enter min,max range of school groups per school.  a teacher will be created per school group (default is 5,20)");
        $schoolGroupRange = $this->extractDelimitedRange($schoolGroupsInput);
        if(!$schoolGroupRange) $schoolGroupRange = (object) ['min'=>5, 'max'=>20];

        //learners
        $learnersInput = $this->ask("enter min,max range of learners per school group (default is 5,50)");
        $learnersRange = $this->extractDelimitedRange($learnersInput);
        if(!$learnersRange) $learnersRange = (object) ['min'=>5, 'max'=>50];

        //begin seeding
        $daysOfTheWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        $clearTC = $this->ask("truncate and start from new? y/n (default is n, so will add more schools incrementally using existing district offices and school year)");
        $doTruncate = strtolower($clearTC) == 'y';

        //teacher payroll
        $createPayrollTC = $this->ask("create new payroll dataset? y/n (default is n)");
        $createPayroll = strtolower($createPayrollTC) == 'y';

        if($createPayroll){
            $payrollDefault = 35000;
            $payrollTC = $this->ask("enter number of teachers on payroll (default is $payrollDefault)");
            $payrollTC = $this->checkInputIsValidInteger($payrollTC, $payrollDefault);
        }

        if($doTruncate){
            //get existing data
            $districtOffices = DB::table('district_office')->select()->get();

            $this->districtOffices = array_map(function($item) {
                return (array)$item;
            }, $districtOffices->toArray());

            //clear populated dbs
//            DB::table('district_office')->truncate();
//            DB::table('district_office')->insert($this->districtOffices);

            DB::table('school')->truncate();

            $this->generateSchoolData($schoolsTC);

//            DB::table('school')->insert($this->schools);

            DB::table('school_academic_year')->truncate();
            DB::table('school_academic_year')->insert($this->academicYear);

            DB::table('school_group')->truncate();
            DB::table('teacher')->truncate();
            DB::table('teacher_timetable')->truncate();
            DB::table('person')->truncate();
            DB::table('media_photo')->truncate();
            DB::table('person_fingerprint')->truncate();
            DB::table('learner')->truncate();
            DB::table('school_learner_admission')->truncate();
            DB::table('school_learner_enrolment')->truncate();
            DB::table('person_attendance')->truncate();

            if($createPayroll){
                DB::table('teacher_payroll')->truncate();
            }

        }else{
            //get existing data
            $districtOffices = DB::table('district_office')->select()->get();

            $this->districtOffices = array_map(function($item) {
                return (array)$item;
            }, $districtOffices->toArray());


            $schoolAcademicYear = DB::table('school_academic_year')->first();
            $this->academicYear = (array) $schoolAcademicYear;

            $this->generateSchoolData($schoolsTC);

//            DB::table('school')->insert($this->schools);

        }

        $this->setExampleData();

        foreach($this->schools as $schoolIdx=>$school){
            $startTime = microtime(true);
            $this->school = $school;
            //create school groups (bodies and enrolment)
            $schoolGroupC = rand($schoolGroupRange->min, $schoolGroupRange->max);
            $this->info("creating $schoolGroupC school groups for school $schoolIdx");

            for($sgIdx = 1; $sgIdx <= $schoolGroupC; $sgIdx++){
                $schoolGroupUuid = Str::orderedUuid();
                $teacherPersonUuid = $this->createPerson($schoolIdx.$sgIdx,true,true,1950, 2000);
                $teacherUuid = Str::orderedUuid();

                //create school group
                $this->schoolGroups[] = [
                    'uuid' => $schoolGroupUuid,
                    "school_uuid"=>$school['uuid'],
                    'academic_year'=>$academicYearInt,
                    'teacher_uuid'=>$teacherUuid,
                    'school_group_name'=>"Class ".$schoolIdx.'-'.$sgIdx,
                    'school_group_level_oid'=>$this->fetchRandomOption('school_group_level'),
                    "active"=>1,
                    "created_at"=>Utils::dateTimeStamp(),
                    "created_by"=>0,
                    "updated_at"=>Utils::dateTimeStamp(),
                    "updated_by"=>0,
                    "synced_at"=>Utils::dateTimeStamp(),
                    "synced_by"=>-1,
                    "synced_by_install_id"=>-1,
                ];

                //create teacher
                $this->teachers[] = [
                    'uuid' => $teacherUuid,
                    'person_uuid'=>$teacherPersonUuid,
                    'school_uuid'=>$school['uuid'],
                    'employment_status_oid'=>$this->fetchRandomOption('employment_status'),
                    'pin'=>rand(100000,999999),
                    'teacher_role_oid'=>$this->fetchRandomOption('teacher_role'),
                    'nassit_number'=>$this->getNassit(),
                    'start_date'=>date('Y-m-d'),
                    'active'=>1,
                    "created_at"=>Utils::dateTimeStamp(),
                    "created_by"=>0,
                    "updated_at"=>Utils::dateTimeStamp(),
                    "updated_by"=>0,
                    "synced_at"=>Utils::dateTimeStamp(),
                    "synced_by"=>-1,
                    "synced_by_install_id"=>-1,
                ];

                //create a timetable for teacher
                foreach($daysOfTheWeek as $dayName){
                    for($hour = 8; $hour <= 15; $hour++){
                        $this->teacherTimetable[] = [
                           'uuid'=>Str::orderedUuid(),
                            'teacher_uuid'=>$teacherUuid,
                            'school_uuid'=>$school['uuid'],
                            'school_group_uuid'=>$schoolGroupUuid,
                            'school_subject_oid'=>$this->fetchRandomOption('school_subject'), //note difference
                            'day_of_the_week_oid'=>$dayName,
                            'start_time'=>date('H:i:s', strtotime("$hour:00:00")),
                            'end_time'=>date('H:i:s', strtotime("$hour:59:00")),
                            "created_at"=>Utils::dateTimeStamp(),
                            "created_by"=>0,
                            "updated_at"=>Utils::dateTimeStamp(),
                            "updated_by"=>0,
                            "synced_at"=>Utils::dateTimeStamp(),
                            "synced_by"=>-1,
                            "synced_by_install_id"=>-1,
                        ];
                    }
                }

                //create teacher attendance
                $this->insertAttendanceForPeriod($this->academicYear['date_from'],$attendanceTC, $teacherPersonUuid, 'teacher', $academicYearInt, $school['uuid'], $schoolGroupUuid);

                //create learners for this school group
                $learnersC = rand($learnersRange->min, $learnersRange->max);
                for($lIdx = 1; $lIdx <= $learnersC; $lIdx++){
                    //learner
                    $learnerPersonUuid = $this->createPerson($schoolIdx.$sgIdx.$lIdx, false, false, 2005, 2018);
                    $learnerGuardianUuid = $this->createPerson($schoolIdx.$sgIdx.$lIdx.'g',true,true, 1950, 2000);
                    $learnerUuid = Str::orderedUuid();
                    $this->learners[] = [
                        'uuid' => $learnerUuid,
                        'person_uuid'=>$learnerPersonUuid,
                        'learner_id'=>'ID_PLACEHOLDER',
                        'language_oid_strongest'=>$this->fetchRandomOption('language'),
                        'maternal_status_oid'=>$this->fetchRandomOption('maternal_status'),
                        'disability_severity_oid_vision'=>$this->fetchRandomOption('disability_severity'),
                        'disability_severity_oid_hearing'=>$this->fetchRandomOption('disability_severity'),
                        'disability_severity_oid_mobility'=>$this->fetchRandomOption('disability_severity'),
                        'disability_severity_oid_cognition'=>$this->fetchRandomOption('disability_severity'),
                        'disability_severity_oid_selfcare'=>$this->fetchRandomOption('disability_severity'),
                        'disability_severity_oid_communication'=>$this->fetchRandomOption('disability_severity'),
                        'disability_other_condition_oid'=>$this->fetchRandomOption('disability_other_condition'),
                        'guardian_person_uuid'=>$learnerGuardianUuid,
                        'guardian_relation_to_learner_oid'=>$this->fetchRandomOption('guardian_relation_to_learner'),
                        "created_at"=>Utils::dateTimeStamp(),
                        "created_by"=>0,
                        "updated_at"=>Utils::dateTimeStamp(),
                        "updated_by"=>0,
                        "synced_at"=>Utils::dateTimeStamp(),
                        "synced_by"=>-1,
                        "synced_by_install_id"=>-1,
                    ];

                    //learner admission
                    $this->learnerAdmissions[] = [
                        'uuid' => Str::orderedUuid(),
                        'school_uuid'=>$school['uuid'],
                        'learner_uuid'=>$learnerUuid,
                        'start_date'=>date('Y-m-d', strtotime("1-1-".$academicYearInt)),
                        "created_at"=>Utils::dateTimeStamp(),
                        "created_by"=>0,
                        "updated_at"=>Utils::dateTimeStamp(),
                        "updated_by"=>0,
                        "synced_at"=>Utils::dateTimeStamp(),
                        "synced_by"=>-1,
                        "synced_by_install_id"=>-1,
                    ];

                    //learner enrolment
                    $this->learnerEnrolments[] = [
                        'uuid' => $learnerUuid,
                        'academic_year'=>$academicYearInt,
                        'learner_uuid'=>$learnerUuid,
                        'school_group_uuid'=>$schoolGroupUuid,
                        "created_at"=>Utils::dateTimeStamp(),
                        "created_by"=>0,
                        "updated_at"=>Utils::dateTimeStamp(),
                        "updated_by"=>0,
                        "synced_at"=>Utils::dateTimeStamp(),
                        "synced_by"=>-1,
                        "synced_by_install_id"=>-1,
                    ];

                    $this->insertAttendanceForPeriod($this->academicYear['date_from'],$attendanceTC, $learnerPersonUuid, 'learner', $academicYearInt, $school['uuid'], $schoolGroupUuid);
                }

            }
            //do inserts for entire school
            $this->line("generating data completed: ".round(microtime(true) - $startTime,0) . " seconds elapsed");
            $startTime = microtime(true);
            $this->doInsertForSchool();
            $this->line("inserting data completed: ".round(microtime(true) - $startTime,0) . " seconds elapsed");
        }

        //teacher payroll
        if($createPayroll) {
            $this->info("seeding payroll table with $payrollTC items");
            $startTime = microtime(true);
            $payrollItems = [];
            for ($prIdx = 1; $prIdx <= $payrollTC; $prIdx++) {
                $pin = 100000 + $prIdx;
                $payrollItem = [
                    'uuid' => Str::orderedUuid(),
                    'yearmonth' => date('Ym'),
                    'school_sid' => rand(10000, 99999),
                    'school_emis_id' => rand(10000, 99999),
                    'first_name' => $this->getRandomFirstname(),
                    'middle_name' => $this->getRandomMiddlename(),
                    'last_name' => $this->getRandomLastname(),
                    'sex' => (rand(0, 1)) ? 'male' : 'female',
                    'date_of_birth' => date('Y-m-d', strtotime(rand(1, 30) . "-" . rand(1, 12) . "-" . rand(1950, 2000))),
                    'pin' => $pin,
                    'nin' => $this->getNin(),
                    'nassit_number' => $this->getNassit(),
                    'created_at' => Utils::dateTimeStamp(),
                    'updated_at' => Utils::dateTimeStamp(),
                    'synced_at' => Utils::dateTimeStamp(),
                ];
                $payrollItems[] = $payrollItem;
            }

            $chunky = array_chunk($payrollItems, 3000, true);

            foreach ($chunky as $chunk) {
                DB::table('teacher_payroll')->insert($chunk);
            }

        }

        $this->line("inserting data completed: " . round(microtime(true) - $startTime, 0) . " seconds elapsed");

        $this->line("seeding completed: " . round((microtime(true) - $beginTime) / 60, 0) . " minutes elapsed");

    }

    function generateSchoolData($schoolsTC){
        for ($i = 1; $i <= $schoolsTC; $i++) {
            $this->schools[] = [
                'uuid' => Str::orderedUuid(),
                "name"=>uniqid("school "),
                "school_education_level_oid"=>$this->fetchRandomOption('school_education_level'),
                "district_office_uuid"=>$this->getRandomUuid($this->districtOffices),
                "active"=>1,
                "created_at"=>Utils::dateTimeStamp(),
                "created_by"=>0,
                "updated_at"=>Utils::dateTimeStamp(),
                "updated_by"=>0,
                "synced_at"=>Utils::dateTimeStamp(),
                "synced_by"=>-1,
                "synced_by_install_id"=>-1,
            ];
        }
    }

    function insertAttendanceForPeriod($dateStart, $daysNo , $person_uuid, $entity_type, $academic_year, $school_uuid, $school_group_uuid){

        for ($dayNo=0; $dayNo <= $daysNo; $dayNo++) {

            $date = date('Y-m-d', strtotime($dateStart." + $dayNo days"));
            $dayOfWeek = date('w', strtotime($date));

            if($dayOfWeek != 6 || $dayOfWeek != 7) {

                $attendanceDateTime = date('Y-m-d H:i:s', strtotime($date . " " . rand(8, 12) . ":" . rand(1, 59) . ":" . rand(1, 59)));

                $attendance_am_status_oid = null;
                $attendance_pm_status_oid = null;
                $attendance_status_oid = null;
                $absent_reason_oid = null;
                $absent_reason_other = null;
                $biometric_method_oid = null;
                $biometric_reference = null;

                if($entity_type=='learner'){
                    $attendance_am_status_oid = $this->fetchRandomOption('attendance_status');
                    $attendance_pm_status_oid = $this->fetchRandomOption('attendance_status');
                }elseif($entity_type=='teacher'){
                    $attendance_status_oid = $this->fetchRandomOption('attendance_status');
                    if ($attendance_status_oid == 'absent'){
                        $absent_reason_oid = $this->fetchRandomOption('absent_reason_teacher');
                    }else{
                        $biometric_method_oid = $this->fetchRandomOption('biometric_method');
                        if ($biometric_method_oid == 'photo') {
                            $biometric_reference = Str::orderedUuid();
                            $this->createMediaPhoto($biometric_reference,$person_uuid,$attendanceDateTime);
                        } elseif ($biometric_method_oid == 'fingerprint') {
                            $biometric_reference = 'fp';
                        }
                    }
                }

                $this->personAttendance[] = [
                    'uuid' => Str::orderedUuid(),
                    'date' => $date,
                    'person_uuid' => $person_uuid,
                    'entity_type_oid' => $entity_type,
                    'academic_year' => $academic_year,
                    'school_uuid' => $school_uuid,
                    'school_group_uuid' => $school_group_uuid,
                    'attendance_am_status_oid' => $attendance_am_status_oid,
                    'attendance_pm_status_oid' => $attendance_pm_status_oid,
                    'attendance_status_oid' => $attendance_status_oid,
                    'absent_reason_oid' => $absent_reason_oid,
                    'absent_reason_other' => $absent_reason_other,
                    'lat' => 9.97179,
                    'lng' => -11.2814,
                    'biometric_method_oid' => $biometric_method_oid,
                    'biometric_reference' => $biometric_reference,
                    'submitted' => 1,
                    "created_at" => $attendanceDateTime,
                    "created_by" => 0,
                    "updated_at" => $attendanceDateTime,
                    "updated_by" => 0,
                    "synced_at"=>Utils::dateTimeStamp(),
                    "synced_by"=>-1,
                    "synced_by_install_id"=>-1,
                ];
            }
        }

    }

    function setExampleData(){
        $this->examplePhotoData = file_get_contents(storage_path('example/seed-img.txt'));
        $this->exampleFingerprintData = file_get_contents(storage_path('example/seed-fp.txt'));

        $this->exampleFirstnames = explode(",", file_get_contents(storage_path('example/seed-firstnames.txt')));
        $this->exampleMiddlenames = explode(",", file_get_contents(storage_path('example/seed-middlenames.txt')));
        $this->exampleLastnames = explode(",", file_get_contents(storage_path('example/seed-lastnames.txt')));
    }

    function getRandomFirstname(){
        return $this->getRandom($this->exampleFirstnames, "fn");
    }
    function getRandomMiddlename(){
        return (rand(0,1)) ? $this->getRandom($this->exampleMiddlenames, "mn") : "";
    }
    function getRandomLastname(){
        return $this->getRandom($this->exampleLastnames, "ln");
    }
    function getRandom($arrayInput,$fallbackPrefix){
        $count = count($arrayInput);
        if($count>0){
            return $arrayInput[rand(0,$count-1)];
        }else{
            return uniqid($fallbackPrefix);
        }
    }

    function getNin(){
        //8 char and must not contain O,L,I,U
        return $this->generateRandomString(8, 'alphanumeric', 'upper', 'FAKE', ['O','L','I','U']);
    }
    function getNassit(){
        //17 char
        return $this->generateRandomString(17, 'alphanumeric', 'upper', 'NASSITFAKE');
    }
    function getPhone(){
        //8 char start with 0 and 2nd digit 3,7,8,9
        $prefixes = [3,7,8,9];
        return $this->generateRandomString(8, 'numeric', '', $prefixes[rand(0,3)]);
    }

    function getLat(){

    }

    function generateRandomString($length = 10, $charType='alphanumeric', $case='mixed', $prefix='', $exclusions=[]): string{
        $numbers = '0123456789';
        $alpha = 'abcdefghijklmnopqrstuvwxyz';

        $x = match($charType){
            'numeric','alphanumeric' => $numbers,
            'alpha' => '',
        };

        if(str_contains($charType, 'alpha')){
            $x .= match($case){
                'mixed' => strtoupper($alpha).strtolower($alpha),
                'upper' => strtoupper($alpha),
                'lower' => strtolower($alpha)
            };
        }

        if(!empty($exclusions)){
            foreach($exclusions as $exclude){
                $x = str_replace($exclude, '', $x);
            }
        }

        return substr($prefix.str_shuffle(str_repeat($x, ceil($length/strlen($x)) )),0,$length);
    }

    function createMediaPhoto($biometric_reference,$person_uuid,$dateTime){
        $this->mediaPhotos[] = [
            'uuid' => $biometric_reference,
            'ref_uuid' => $person_uuid,
            'base64_data' => $this->examplePhotoData,
            'display_orientation' => 0,
            'active' => 1,
            "created_at" => $dateTime,
            "created_by" => 0,
            "updated_at" => $dateTime,
            "updated_by" => 0,
            "synced_at"=>$dateTime,
            "synced_by"=>-1,
            "synced_by_install_id"=>-1,
        ];
    }
    function createPerson($suffix = '',$createPhoto=false,$createFps=false, $dobRangeStart, $dobRangeEnd){
        if(!$suffix) $suffix = uniqid();
        $personUuid = Str::orderedUuid();
        $portraitUuid = Str::orderedUuid();
        $fps = (object) [
            'fp_lt_uuid' => Str::orderedUuid(),
            'fp_li_uuid' => Str::orderedUuid(),
            'fp_rt_uuid' => Str::orderedUuid(),
            'fp_ri_uuid' => Str::orderedUuid()
        ];

        $this->persons[] = [
            'uuid'=>$personUuid,
            'first_name'=>$this->getRandomFirstname(),
            'middle_name'=>$this->getRandomMiddlename(),
            'last_name'=>$this->getRandomLastname(),
            'sex_oid'=> (rand(0,1)) ? 'male' : 'female',
//            'date_of_birth'=>date('Y-m-d', strtotime(rand(1,30)."-".rand(1,12)."-".rand(1950,2000))),
            'date_of_birth'=>date('Y-m-d', strtotime(rand(1,30)."-".rand(1,12)."-".rand($dobRangeStart,$dobRangeEnd))),
            'nin'=>$this->getNin(),
            'portrait_uuid'=>$portraitUuid,
            'phone_1'=>$this->getPhone(),
            'email'=>uniqid()."@".uniqid().".com",
            'fp_lt_uuid'=>$fps->fp_lt_uuid,
            'fp_li_uuid'=>$fps->fp_li_uuid,
            'fp_rt_uuid'=>$fps->fp_rt_uuid,
            'fp_ri_uuid'=>$fps->fp_ri_uuid,
            "created_at" => Utils::dateTimeStamp(),
            "created_by" => 0,
            "updated_at" => Utils::dateTimeStamp(),
            "updated_by" => 0,
            "synced_at"=>Utils::dateTimeStamp(),
            "synced_by"=>-1,
            "synced_by_install_id"=>-1,
        ];

        if($createPhoto) {
            $this->createMediaPhoto($portraitUuid,$personUuid,Utils::dateTimeStamp());
        }

        if($createFps) {
            $i = 0;
            $arrFps = ['lt', 'li', 'rt', 'ri'];
            foreach ($fps as $fp) {
                $this->personFingerprints[] = [
                    'uuid' => Str::orderedUuid(),
                    'person_uuid' => $personUuid,
                    'finger_position_oid' => $arrFps[$i],
                    'fp_a_cbor' => $this->exampleFingerprintData,
                    "fp_a_nfiq" => rand(1, 5),
                    'fp_b_cbor' => $this->exampleFingerprintData,
                    "fp_b_nfiq" => rand(1, 5),
                    "created_at" => Utils::dateTimeStamp(),
                    "created_by" => 0,
                    "updated_at" => Utils::dateTimeStamp(),
                    "updated_by" => 0,
                    "synced_at"=>Utils::dateTimeStamp(),
                    "synced_by"=>-1,
                    "synced_by_install_id"=>-1,
                ];
                $i++;
            }
        }

        return $personUuid;
    }

    function doInsertForSchool(){
        if($this->school){
            DB::table('school')->insert($this->school);
        }

        if($this->schoolGroups){
            $this->doChunkyInsert($this->schoolGroups, 'school_group');
            $this->schoolGroups = [];
        }
        if($this->teachers){
            $this->doChunkyInsert($this->teachers, 'teacher');
            $this->teachers = [];
        }
        if($this->persons){
            $this->doChunkyInsert($this->persons, 'person');
            $this->persons = [];
        }
        if($this->mediaPhotos){
            $this->doChunkyInsert($this->mediaPhotos, 'media_photo');
            $this->mediaPhotos = [];
        }
        if($this->personFingerprints){
            $this->doChunkyInsert($this->personFingerprints, 'person_fingerprint');
            $this->personFingerprints = [];
        }
        if($this->teacherTimetable){
            $this->doChunkyInsert($this->teacherTimetable, 'teacher_timetable');
            $this->teacherTimetable = [];
        }
        if($this->personAttendance){
            $this->doChunkyInsert($this->personAttendance, 'person_attendance');
            $this->personAttendance = [];
        }
        if($this->learners) {
            $this->doChunkyInsert($this->learners, 'learner');
            $this->learners = [];
        }
        if($this->learnerAdmissions) {
            $this->doChunkyInsert($this->learnerAdmissions, 'school_learner_admission');
            $this->learnerAdmissions = [];
        }
        if($this->learnerEnrolments){
            $this->doChunkyInsert($this->learnerEnrolments, 'school_learner_enrolment');
            $this->learnerEnrolments = [];
        }
    }

    function doChunkyInsert($bigArray, $tableName): void
    {
        $chunky = array_chunk($bigArray, 2000, true);

        foreach($chunky as $chunk){
            DB::table($tableName)->insert($chunk);
        }
    }

    function getRandomUuid($arrayWithUuid){
        $count = count($arrayWithUuid);
        if($count==0) return uniqid("empty_array_");
        if(!isset($arrayWithUuid[0]['uuid'])) return uniqid("no_uuid_");
        $idx = rand(0,$count-1);
        return $arrayWithUuid[$idx]['uuid'];
    }

    function extractDelimitedRange($input, $separator = ","): object|bool
    {
        if(empty($input)) return false;
        if(!str_contains($input,$separator)) return false;
        $explode = explode($separator,$input);
        $min = (int) $explode[0];
        $max = (int) $explode[1];
        if($min==0 || $min<0) return false;
        if($max==0 || $max<0) return false;
        if($min>$max) return false;
        return (object) ['min'=>$min, 'max'=>$max];
    }
    function checkInputIsValidInteger($input, $defaultInt){
        $input = (int) $input;
        if(empty($input) || $input==0 || $input<0){
            $this->warn("WARN: input empty or not valid, defaulting to $defaultInt");
            return $defaultInt;
        }else{
            return $input;
        }
    }

    function fetchRandomOption($listName){
        return DB::table('option_list')->where('list_name',$listName)->where('active',1)->inRandomOrder()->value('item_id');
    }

}
