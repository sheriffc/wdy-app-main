<?php

namespace App\Api;

use App\Common\Utils;
use App\Services\LearnerIdService;
use Illuminate\Database\QueryException;
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
                    if ($table === 'school') {
                        foreach (['wash_oids', 'electricity_oids', 'mno_oids', 'learning_materials_oids'] as $field) {
                            if (isset($record[$field])) {
                                $record[$field] = self::enforceNoneExclusive($record[$field]);
                            }
                        }
                    }

                    if ($table === 'learner') {
                        $record = self::persistLearnerRecord($record);
                        $payload[$table][] = ["pkCn"=>"uuid","id"=>$record['uuid'],"learner_id"=>$record['learner_id']];
                        continue;
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

    /**
     * Insert/update a learner record without relying on upsert()'s "ON DUPLICATE
     * KEY UPDATE" semantics: once learner_id is uniquely indexed, upsert() keyed
     * on uuid alone would silently overwrite a *different* learner's row if the
     * incoming learner_id collides with it (MySQL's ODKU fires on any unique key
     * match, not just the one named). This does an explicit exists-check first.
     *
     * Ownership rule: once a learner_id is set server-side, it always wins over
     * whatever the client re-uploads — this keeps a re-synced/edited learner from
     * "colliding with itself" and burning a fresh sequence number every sync.
     */
    private static function persistLearnerRecord(array $record): array
    {
        $existing = DB::table('learner')->where('uuid', $record['uuid'])->first();

        if ($existing && !empty($existing->learner_id)) {
            $record['learner_id'] = $existing->learner_id;
        } elseif (!empty($record['learner_id'])) {
            $record['learner_id'] = self::resolveLearnerIdCollision($record['uuid'], $record['learner_id']);
        }

        if ($existing) {
            DB::table('learner')->where('uuid', $record['uuid'])->update($record);
            return $record;
        }

        try {
            DB::table('learner')->insert($record);
        } catch (QueryException $e) {
            // SQLSTATE 23000: integrity constraint violation (e.g. duplicate learner_id
            // from a near-simultaneous upload). Recompute and retry once.
            if ($e->getCode() !== '23000') {
                throw $e;
            }
            if (!empty($record['learner_id'])) {
                $record['learner_id'] = self::resolveLearnerIdCollision($record['uuid'], $record['learner_id'], true);
            }
            DB::table('learner')->insert($record);
        }

        return $record;
    }

    private static function resolveLearnerIdCollision(string $uuid, string $learnerId, bool $forceRegenerate = false): string
    {
        $collision = $forceRegenerate || DB::table('learner')
            ->where('learner_id', $learnerId)
            ->where('uuid', '<>', $uuid)
            ->exists(); // no deleted_at filter — soft-deleted rows still hold their id

        if (!$collision) {
            return $learnerId;
        }

        $parsed = LearnerIdService::parse($learnerId);
        if (!$parsed) {
            return $learnerId;
        }

        $newLearnerId = LearnerIdService::format(
            $parsed['prefix'],
            $parsed['year'],
            LearnerIdService::nextAvailable($parsed['prefix'], $parsed['year'])
        );

        logger()->warning('learner_id collision auto-resolved', [
            'uuid' => $uuid,
            'submitted_learner_id' => $learnerId,
            'new_learner_id' => $newLearnerId,
        ]);

        return $newLearnerId;
    }

    /**
     * When 'none' is present in a comma-separated OID string, drop every other value.
     */
    private static function enforceNoneExclusive(?string $oidsString): ?string
    {
        if (empty($oidsString)) return $oidsString;
        $parts = array_filter(array_map('trim', explode(',', $oidsString)));
        if (in_array('none', $parts, true)) return 'none';
        return implode(',', $parts);
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

                    if ($table === 'school') {
                        foreach (['wash_oids', 'electricity_oids', 'mno_oids', 'learning_materials_oids'] as $field) {
                            if (isset($record[$field])) {
                                $record[$field] = self::enforceNoneExclusive($record[$field]);
                            }
                        }
                    }

                    if ($table === 'learner') {
                        $record = self::persistLearnerRecord($record);
                        $payload[$table][] = ["pkCn"=>"uuid","id"=>$record['uuid'],"learner_id"=>$record['learner_id']];
                        continue;
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
            'school_feeding'=>'schoolFeeding',
            'school_feeding_stock'=>'schoolFeedingStock',
            'learner_performance'=>'learnerPerformance',
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
