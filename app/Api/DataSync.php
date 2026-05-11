<?php

namespace App\Api;

use App\Common\Utils;
use Illuminate\Support\Facades\DB;

class DataSync
{

    public static function processUploadData($params, $user, $schoolIds, $schoolIdsString){
        $payload = [];

        foreach($params['data'] as $table=>$records){
            if($records){
                foreach($records as $record){
                    $record = $record['nameValuePairs'];
                    if(isset($record['sync_flag'])){
                        unset($record['sync_flag']);
                    }
                    $affected = DB::table($table)->upsert(
                        $record,
                        ['uuid']
                    );

                    if($affected){
                        $payload[$table][] =  ["pkCn"=>"uuid","id"=>$record['uuid']];
                    }
                }
            }

        }
        return $payload;
    }

    public static function processUploadDataVersion3($params, $user, $schoolIds, $schoolIdsString, $installId){
        $payload = [];

        foreach($params['data'] as $table=>$records){
            if($records){
                foreach($records as $record){
                    $record = $record['nameValuePairs'];
                    if(isset($record['sync_flag'])){
                        unset($record['sync_flag']);
                    }
                    //actually no change as ts is logged in mysql on update
                    if(!isset($record['synced_at'])){
                        $record['synced_at'] = Utils::dateTimeStamp();
                    }

                    if(isset($user)){
                        $record['synced_by'] = $user->id;
                    }

                    if(isset($installId)) {
                        $record['synced_by_install_id'] = $installId;
                    }

                    $affected = DB::table($table)->upsert(
                        $record,
                        ['uuid']
                    );

                    // Deduplicate enrolments: one active record per learner+academic_year.
                    // If this record is active (not a deletion) and has a class assigned,
                    // soft-delete any other non-deleted enrolments for the same learner+year.
                    if ($table === 'school_learner_enrolment'
                        && !empty($record['learner_uuid'])
                        && !empty($record['academic_year'])
                        && empty($record['deleted_at'])
                        && !empty($record['school_group_uuid'])
                    ) {
                        DB::table('school_learner_enrolment')
                            ->where('learner_uuid', $record['learner_uuid'])
                            ->where('academic_year', $record['academic_year'])
                            ->where('uuid', '<>', $record['uuid'])
                            ->whereNull('deleted_at')
                            ->update(['deleted_at' => Utils::dateTimeStamp()]);
                    }

                    if($affected){
                        $payload[$table][] =  ["pkCn"=>"uuid","id"=>$record['uuid']];
                    }
                }
            }

        }
        return $payload;
    }
    public static function spoofUploadData($params, $user, $schoolIds, $schoolIdsString): array {
        $payload = [];

        foreach($params['data'] as $table=>$records){
            if($records){
                foreach($records as $record){
                    $record = $record['nameValuePairs'];
                    if(isset($record['sync_flag'])){
                        unset($record['sync_flag']);
                    }

                    $payload[$table][] =  ["pkCn"=>"uuid","id"=>$record['uuid']];
                }
            }

        }
        return $payload;
    }

    public static function preparePayloadVersion0($params, $user, $schoolIds, $schoolIdsString){

        //check if database has the migration run already
        $rec = DB::table("migrations")->where('migration','LIKE', '%v11_changes%')->first();
        $v11MigrationHasRun = false;
        if($rec){
           if($rec->batch > 0){
               $v11MigrationHasRun = true;
           }
        }

        $queryClassName = ($v11MigrationHasRun) ? '\DataSyncQueriesV0PostMig::' : '\DataSyncQueriesV0::';

        $limit = 5000;
        $payloadCounter = 0;

        $payload = [];
        $uniConfig = [
            'non_payroll_teachers'=>'nonPayrollTeachers',
            'district_office'=>'districtOffice',
            'school_academic_year'=>'schoolAcademicYear',
            'teacher_payroll'=>'teacherPayroll',
            'option_list'=>'optionList',
            'geo'=>'geo',
        ];
        $biConfig = [
            'learner'=>'learner',
            'person'=>'person',
            'person_attendance'=>'personAttendance',
            'school'=>'school',
            'school_group'=>'schoolGroup',
            'school_learner_admission'=>'schoolLearnerAdmission',
            'school_learner_enrolment'=>'schoolLearnerEnrolment',
            'teacher'=>'teacher',
            'media_photo'=>'mediaPhoto',
            'person_fingerprint'=>'personFingerprint',
            'teacher_timetable'=>'teacherTimetable',
        ];

        foreach($params['table_states_uni_tables'] as $tableName=>$tableState){
            if($tableState['rec_count'] == 0){
                //do a full reload
                $recs = call_user_func(__NAMESPACE__ . $queryClassName . $uniConfig[$tableName], false,false,"LIMIT $limit");

            }else{
                //do a from
                $recs = call_user_func(__NAMESPACE__ . $queryClassName . $uniConfig[$tableName], $tableState['max_updated_at'],$tableState['max_pk'],"LIMIT $limit");
            }
            if($recs){
                $payload[$tableName] = $recs;
                $payloadCounter = $payloadCounter + count($recs);
                if($payloadCounter >= $limit) return $payload;
            }
        }

        foreach($params['table_states_bi_tables'] as $schoolId=>$tableStates){
            //check they have visibility
            if(in_array($schoolId,$schoolIds)) {

                foreach ($tableStates as $tableName => $tableState) {

                    $whereIn = "'$schoolId'";

                    if ($tableState['rec_count'] == 0) {
                        //do a full reload
                        $recs = call_user_func(__NAMESPACE__ . $queryClassName . $biConfig[$tableName], $whereIn,false, false, "LIMIT $limit");

                    } else {
                        //do a from
                        $recs = call_user_func(__NAMESPACE__ . $queryClassName . $biConfig[$tableName], $whereIn,$tableState['max_updated_at'], $tableState['max_pk'], "LIMIT $limit");
                    }
                    if ($recs) {
                        //this is additive
                        if(isset($payload[$tableName])){
                            if($payload[$tableName]){
                                $payload[$tableName] = array_merge($recs,$payload[$tableName]);
                            }
                        }else{
                            $payload[$tableName] = $recs;
                        }
                        $payloadCounter = $payloadCounter + count($recs);
                        if ($payloadCounter >= $limit) return $payload;
                    }
                }

            }
        }

//        logger(print_r($payload,true));

        return $payload;
    }
    public static function preparePayloadVersion1($params, $user, $schoolIds, $schoolIdsString){
        $limit = 5000;
        $payloadCounter = 0;

        $payload = [];
        $uniConfig = [
            'non_payroll_teachers'=>'nonPayrollTeachers',
            'district_office'=>'districtOffice',
            'school_academic_year'=>'schoolAcademicYear',
            'teacher_payroll'=>'teacherPayroll',
            'option_list'=>'optionList',
            'option_list_link'=>'optionListLink',
            'geo'=>'geo',
        ];
        $biConfig = [
            'learner'=>'learner',
            'person'=>'person',
            'person_attendance'=>'personAttendance',
            'school'=>'school',
            'school_group'=>'schoolGroup',
            'school_learner_admission'=>'schoolLearnerAdmission',
            'school_learner_enrolment'=>'schoolLearnerEnrolment',
            'teacher'=>'teacher',
            'media_photo'=>'mediaPhoto',
            'person_fingerprint'=>'personFingerprint',
            'teacher_timetable'=>'teacherTimetable',
        ];

        foreach($params['table_states_uni_tables'] as $tableName=>$tableState){
            if($tableState['rec_count'] == 0){
                //do a full reload
                $recs = call_user_func(__NAMESPACE__ .'\DataSyncQueriesV1::'.$uniConfig[$tableName], false,false,"LIMIT $limit");

            }else{
                //do a from
                $recs = call_user_func(__NAMESPACE__ .'\DataSyncQueriesV1::'.$uniConfig[$tableName], $tableState['max_updated_at'],$tableState['max_pk'],"LIMIT $limit");
            }
            if($recs){
                $payload[$tableName] = $recs;
                $payloadCounter = $payloadCounter + count($recs);
                if($payloadCounter >= $limit) return $payload;
            }
        }

        foreach($params['table_states_bi_tables'] as $schoolId=>$tableStates){
            //check they have visibility
            if(in_array($schoolId,$schoolIds)) {

                foreach ($tableStates as $tableName => $tableState) {

                    $whereIn = "'$schoolId'";

                    if ($tableState['rec_count'] == 0) {
                        //do a full reload
                        $recs = call_user_func(__NAMESPACE__ . '\DataSyncQueriesV1::' . $biConfig[$tableName], $whereIn,false, false, "LIMIT $limit");

                    } else {
                        //do a from
                        $recs = call_user_func(__NAMESPACE__ . '\DataSyncQueriesV1::' . $biConfig[$tableName], $whereIn,$tableState['max_updated_at'], $tableState['max_pk'], "LIMIT $limit");
                    }
                    if ($recs) {
                        //this is additive
                        if(isset($payload[$tableName])){
                            if($payload[$tableName]){
                                $payload[$tableName] = array_merge($recs,$payload[$tableName]);
                            }
                        }else{
                            $payload[$tableName] = $recs;
                        }
                        $payloadCounter = $payloadCounter + count($recs);
                        if ($payloadCounter >= $limit) return $payload;
                    }
                }

            }
        }

//        $payload['synced_at'] = [Utils::dateTimeStamp()];

//        logger(print_r($payload,true));

        return $payload;
    }
    public static function preparePayloadVersion3($params, $user, $schoolIds, $schoolIdsString){

        /** major change to improve sync by communicating table state scoped by school
         *  - addition of synced_at timestamp to capture time the affected record was received by server
         *  - return with each payload the max synced_at timestamp and pk for each school and table
         *  - [table_states_uni_tables]=>[table][max_synced_at][max_pk]
         *  - [table_states_bi_tables]=>[school][table][max_synced_at][max_pk]
         */

        $payloadLimit = 5000;
        $payloadCounter = 0;

        $payload = [];
        $uniConfig = [
            'non_payroll_teachers'=>'nonPayrollTeachers',
            'district_office'=>'districtOffice',
            'school_academic_year'=>'schoolAcademicYear',
            'teacher_payroll'=>'teacherPayroll',
            'option_list'=>'optionList',
            'option_list_link'=>'optionListLink',
            'geo'=>'geo',
            'learner'=>'learnerGlobal',
        ];
        $biConfig = [
            'person'=>'person',
            'person_attendance'=>'personAttendance',
            'school'=>'school',
            'school_group'=>'schoolGroup',
            'school_learner_admission'=>'schoolLearnerAdmission',
            'school_learner_enrolment'=>'schoolLearnerEnrolment',
            'teacher'=>'teacher',
            'media_photo'=>'mediaPhoto',
            'person_fingerprint'=>'personFingerprint',
            'teacher_timetable'=>'teacherTimetable',
        ];

        $installId = (isset($params['install_id'])) ? $params['install_id'] : "no_install_id_provided";

        foreach($params['table_states_uni_tables'] as $tableName=>$tableState){
//            if($tableState['rec_count'] == 0 || !isset($tableState['max_synced_at']) || !isset($tableState['max_pk'])){
            //fix to address client not including deleted_at files in rec_count
            if(!isset($tableState['max_synced_at']) || !isset($tableState['max_pk'])){
                //do a full reload
                $recs = collect(call_user_func(__NAMESPACE__ .'\DataSyncQueriesV3::'.$uniConfig[$tableName], false,false,"LIMIT $payloadLimit", false));

            }else{
                //do a from
                $recs = collect(call_user_func(__NAMESPACE__ .'\DataSyncQueriesV3::'.$uniConfig[$tableName], $tableState['max_synced_at'],$tableState['max_pk'],"LIMIT $payloadLimit", $installId));
            }
            if($recs->isNotEmpty()){
                //capture the state and drop field
                $maxPk = '';
                if(isset($recs->first()->id)) $maxPk = strval($recs->max('id'));
                if(isset($recs->first()->uuid)) $maxPk = strval($recs->max('uuid'));

                $maxSyncedAt = $recs->max('synced_at');

                foreach($recs as $i){
                    unset($i->synced_at);
                }

                $recs = $recs->toArray();

                $payload['table_states_uni_tables'][] = ['table_name'=>$tableName,'max_synced_at'=>$maxSyncedAt, 'max_pk'=>$maxPk];

                $payload[$tableName] = $recs;
                $payloadCounter = $payloadCounter + count($recs);
                if($payloadCounter >= $payloadLimit) return $payload;
            }
        }

        /** to be able to communicate state from both sides, previous version needs
         * to be refactored to query for individual school scope rather than in group
         */

        foreach($params['table_states_bi_tables'] as $schoolId=>$tableStates){
            //check they have visibility
            if(in_array($schoolId,$schoolIds)) {

                foreach ($tableStates as $tableName => $tableState) {

                    if (!isset($biConfig[$tableName])) continue;

                    $whereIn = "'$schoolId'";

//                    if ( $tableState['rec_count'] == 0 || !isset($tableState['max_synced_at']) || !isset($tableState['max_pk']) ) {
                    //fix to address client not including deleted_at files in rec_count
                    if (!isset($tableState['max_synced_at']) || !isset($tableState['max_pk']) ) {
                        //do a full reload
                        $recs = collect(call_user_func(__NAMESPACE__ . '\DataSyncQueriesV3::' . $biConfig[$tableName], $whereIn,false, false, "LIMIT $payloadLimit", false));

                    } else {
                        //do a from
                        $recs = collect(call_user_func(__NAMESPACE__ . '\DataSyncQueriesV3::' . $biConfig[$tableName], $whereIn,$tableState['max_synced_at'], $tableState['max_pk'], "LIMIT $payloadLimit", $installId));
                    }
                    if ($recs->isNotEmpty()) {
                        //capture the state and drop field
                        $payload['table_states_bi_tables'][] = ['school_uuid'=>$schoolId,'table_name'=>$tableName,'max_synced_at'=>$recs->max('synced_at'), 'max_pk'=>strval($recs->max('uuid'))];
                        foreach($recs as $i){
                            unset($i->synced_at);
                        }

                        $recs = $recs->toArray();

                        //this is additive
                        if(isset($payload[$tableName])){
                            if($payload[$tableName]){
                                $payload[$tableName] = array_merge($recs,$payload[$tableName]);
                            }
                        }else{
                            $payload[$tableName] = $recs;
                        }
                        $payloadCounter = $payloadCounter + count($recs);
                        if ($payloadCounter >= $payloadLimit) return $payload;
                    }
                }

            }
        }

//        logger(print_r($payload,true));

        return $payload;
    }

}
