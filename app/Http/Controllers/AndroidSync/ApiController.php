<?php

namespace App\Http\Controllers\AndroidSync;

use App\Api\DataSync;
use App\Common\Queries\DbQueries;
use App\Http\Controllers\AndroidController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Common\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    private $user;
    private $userScopeSchoolsString;
    private $userScopeSchools;

    public function __construct()
    {
//        $this->middleware('auth');
    }

    // N.B. upload has client info fields like app_version in the root $request, whereas for download is in $request->params
    public function handleDownloadDataRequest(Request $request){
//        if (env("LOG_SYNC", false)) {
////            logger(print_r($request->all(),true));
//            Log::debug('Download request made', $request->all());
//        }

        if (!$request->has(['access_token','request','params'])) {
            Utils::errorResponse('badly formed request');
        }
        $this->checkToken($request->input('access_token'));

        $this->setSchoolScope();

        $params = $request->input('params');

//        logger(print_r($params,true));
        if (env("LOG_SYNC_REQUEST", false)) {
            Log::debug('Download request params', $params);
        }

        if(!isset($params['scope_cache_id']) ||
            !isset($params['sync_mode']) ||
//            !isset($params['install_id']) ||
            !isset($params['table_states_uni_tables']) ||
            !isset($params['table_states_bi_tables'])
        ) {
            Utils::errorResponse("nothing to return");
        }

        if (!isset($params['app_db_version']) || $params['app_db_version'] < 2
            || !isset($params['app_version']) || $params['app_version'] < 11) {
            // app is too old -- reject request completely
            logger('Old 60-school phase app (DB < 2 or AppVersion < 11) tried to download data; rejected by server. $params: ');
            logger($params);
            Utils::errorResponse("Sync rejected: your app version is too old – you must update your app to continue using this system");
        }else{
            //handle version
            $payloadData = match ($params['app_db_version']) {
                1, 2 => DataSync::preparePayloadVersion1($params, $this->user, $this->userScopeSchools, $this->userScopeSchoolsString),
                default => DataSync::preparePayloadVersion3($params, $this->user, $this->userScopeSchools, $this->userScopeSchoolsString),
            };
        }

        if (env("LOG_SYNC_PAYLOAD", false)) {
//            Log::debug('Download payload returned', $payloadData);
            Log::channel('sync')->debug('Download payload returned', $payloadData);
        }

        Utils::successResponse("success",$payloadData);
    }

    // N.B. upload has client info fields like app_version in the root $request, whereas for download is in $request->params
    public function handleUploadDataRequest(Request $request){
//        logger(print_r($request->all(),true));
        if (!$request->has(['access_token','request','params'])) {
            Utils::errorResponse('badly formed request');
        }
        $this->checkToken($request->input('access_token'));

        $this->setSchoolScope();

        $params = $request->input('params');

//        logger(print_r($params,true));
        if (env("LOG_SYNC_REQUEST", false)) {
            Log::debug('Upload request params', $params);
        }
        if( !isset($params['sync_mode']) ||
            !isset($params['data'])
        ) {
            Utils::errorResponse("badly formed request");
        }

        //for the demo application, spoof uploading
        if (Utils::isDemoEnv()) {
            $payloadData = DataSync::spoofUploadData($params, $this->user, $this->userScopeSchools, $this->userScopeSchoolsString);
        }else {
            if (!isset($request['app_db_version']) || $request['app_db_version'] < 2
                || !isset($request['app_version']) || $request['app_version'] < 11) {
                // app is too old -- reject request completely
                logger('Old 60-school phase app (DB < 2 or AppVersion < 11) tried to upload data; rejected by server. $request: ');
                logger($request);
                Utils::errorResponse("Sync rejected: your app version is too old – you must update your app to continue using this system");
            } else {
                //handle version
//                $payloadData = match ($request['app_db_version']) {
//                    1, 2 => DataSync::processUploadData($params, $this->user, $this->userScopeSchools, $this->userScopeSchoolsString),
//                    default => DataSync::processUploadDataVersion3($params, $this->user, $this->userScopeSchools, $this->userScopeSchoolsString,$installId),
//                };
                switch ($request['app_db_version']) {
                    case 1:
                    case 2:
                        $payloadData = DataSync::processUploadData($params, $this->user, $this->userScopeSchools, $this->userScopeSchoolsString);
                        break;
                    default:
                        $installId = (isset($request['install_id'])) ? $request['install_id'] : "no_install_id_provided";
                        $payloadData = DataSync::processUploadDataVersion3($params, $this->user, $this->userScopeSchools, $this->userScopeSchoolsString,$installId);
                }

//                switch ($request['app_version']) {
//                    case 11:
//                        $payloadData = DataSync::processUploadData($params, $this->user, $this->userScopeSchools, $this->userScopeSchoolsString);
//                        break;
//                    default:
//                        //handle version
//                        $payloadData = match ($request['app_db_version']) {
//                            1, 2 => DataSync::processUploadData($params, $this->user, $this->userScopeSchools, $this->userScopeSchoolsString),
//                            default => DataSync::processUploadDataVersion3($params, $this->user, $this->userScopeSchools, $this->userScopeSchoolsString),
//                        };
//                }
            }
        }

        DB::table("android_api_log")->insert([
                "user_action"=>$request->input("request"),
                "json_data"=>json_encode($request->all())
        ]);

        if (env("LOG_SYNC_PAYLOAD", false)) {
//            Log::debug('Upload payload returned', $payloadData);
            Log::channel('sync')->debug('Upload payload returned', $payloadData);
        }

        Utils::successResponse("success",$payloadData);
    }

    public function checkAndUpdateToken(Request $request){
        if (!$request->has(['access_token','request','params'])) {
            Utils::errorResponse('badly formed request');
        }
        $this->checkToken($request->input('access_token'));

        $this->setSchoolScope();

        $payloadData = [
            "scope_cache_id"=>$this->user->scope_cache_id,
            "scope_hash"=>$this->user->scope_hash,
            "scope_schools"=>$this->userScopeSchoolsString
        ];

        Utils::successResponse("success",$payloadData);
    }

    public function checkIn(Request $request){
        if (!$request->has(['access_token','request','params'])) {
            Utils::errorResponse('badly formed request');
        }

        $this->checkToken($request->input('access_token'));

        $this->setSchoolScope();

        //put in log
        DB::table("android_api_log")->insert([
            "user_action"=>$request->input("request"),
            "json_data"=>json_encode($request->all())
        ]);

        //get app update details
        $userVersionCode = null;
        if (isset($request->params['version_code'])) {
            $userVersionCode = $request->params['version_code'];
        }
        $latestAppVersionDetails = AndroidController::getLatestAppVersion($userVersionCode);

        $payloadData = array_merge((array)$latestAppVersionDetails,[
            "scope_cache_id"=>$this->user->scope_cache_id,
            "scope_hash"=>$this->user->scope_hash,
            "scope_schools"=>$this->userScopeSchoolsString,
            "s3_key"=>env("AWS_ONE_WAY_ACCESS_KEY_ID"),
            "s3_secret"=>env("AWS_ONE_WAY_SECRET_ACCESS_KEY"),
            "s3_bucket"=>env("AWS_BUCKET_MEDIA"),
            "s3_region"=>env("AWS_DEFAULT_REGION"),
        ]);

        Utils::successResponse("success",$payloadData);
    }

    public function setSchoolScope(){
        $cacheId = $this->user->scope_cache_id;

        if(!$cacheId){
            Utils::errorResponse("no permissions set");
        }

        $this->userScopeSchoolsString = DB::table("user_scope_cache")->where("id",$cacheId)->value("school_uuids");
        $this->userScopeSchools = explode(",", $this->userScopeSchoolsString);
    }

    public function checkToken($accessToken): void{
        //check if valid
        $this->user = User::where('client_access_token',$accessToken)
            ->where('mobile_access',1)
            ->where('active',1)
            ->first();

        if(!$this->user) Utils::errorResponse("token invalid");
    }

    public function searchTeachers(Request $request): void {
        if (!$request->has(['access_token', 'request', 'params'])) {
            Utils::errorResponse('badly formed request');
        }
        $this->checkToken($request->input('access_token'));

        $params  = $request->input('params');
        $type    = $params['type']        ?? 'payroll';
        $query   = trim($params['query']  ?? '');
        $schoolUuid = $params['school_uuid'] ?? null;

        if ($type === 'nonpayroll') {
            $teachers = $this->fetchNonPayrollTeachers($query, $schoolUuid);
        } else {
            $teachers = $this->fetchPayrollTeachers($query, $schoolUuid);
        }

        Utils::successResponse('success', ['teachers' => $teachers]);
    }

    private function fetchPayrollTeachers(string $query, ?string $schoolUuid): array {
        $q = DB::table('teacher_payroll as tp')
            ->whereNotNull('tp.pin')
            ->where(function ($q2) {
                $q2->whereNull('tp.deleted_at')->orWhere('tp.deleted_at', '');
            })
            ->selectRaw("tp.uuid, tp.first_name, tp.middle_name, tp.last_name,
                TRIM(REPLACE(CONCAT(COALESCE(tp.last_name,''),', ',COALESCE(tp.first_name,''),' ',COALESCE(tp.middle_name,'')),'  ',' ')) AS full_name,
                tp.sex, tp.date_of_birth, tp.pin, tp.nin, tp.nassit_number, tp.created_at, tp.updated_at");

        if ($schoolUuid) {
            $q->whereNotIn('tp.pin', function ($sub) use ($schoolUuid) {
                $sub->select('pin')->from('teacher')
                    ->where('school_uuid', $schoolUuid)
                    ->where(function ($s) { $s->whereNull('deleted_at')->orWhere('deleted_at', ''); })
                    ->whereNotNull('pin');
            });
        }

        if ($query !== '') {
            foreach (preg_split('/\s+/', $query) as $term) {
                $q->where(function ($q2) use ($term) {
                    $q2->where(DB::raw("CONCAT(COALESCE(tp.last_name,''),', ',COALESCE(tp.first_name,''),' ',COALESCE(tp.middle_name,''))"), 'LIKE', "%{$term}%")
                       ->orWhere('tp.pin', 'LIKE', "%{$term}%");
                });
            }
        }

        // No query → cap at 500 so the initial list is usable; with a query → no cap (search narrows naturally)
        if ($query === '') $q->limit(500);

        return $q->orderByRaw("CONCAT(COALESCE(tp.last_name,''),tp.first_name)")->get()->toArray();
    }

    private function fetchNonPayrollTeachers(string $query, ?string $schoolUuid): array {
        $q = DB::table('teacher_payroll as tp')
            ->whereNull('tp.pin')
            ->where(function ($q2) {
                $q2->whereNull('tp.deleted_at')->orWhere('tp.deleted_at', '');
            })
            ->selectRaw("tp.uuid, tp.first_name, tp.middle_name, tp.last_name,
                TRIM(REPLACE(CONCAT(COALESCE(tp.last_name,''),', ',COALESCE(tp.first_name,''),' ',COALESCE(tp.middle_name,'')),'  ',' ')) AS full_name,
                tp.sex, tp.date_of_birth, '' AS pin, tp.nin, tp.nassit_number, tp.created_at, tp.updated_at");

        if ($schoolUuid) {
            $q->whereNotExists(function ($sub) use ($schoolUuid) {
                $sub->select(DB::raw(1))
                    ->from('teacher as t')
                    ->join('person as p', 'p.uuid', '=', 't.person_uuid')
                    ->where('t.school_uuid', $schoolUuid)
                    ->where(function ($s) { $s->whereNull('t.deleted_at')->orWhere('t.deleted_at', ''); })
                    ->whereRaw('p.nin = tp.nin');
            });
        }

        if ($query !== '') {
            foreach (preg_split('/\s+/', $query) as $term) {
                $q->where(DB::raw("CONCAT(COALESCE(tp.last_name,''),', ',COALESCE(tp.first_name,''),' ',COALESCE(tp.middle_name,''))"), 'LIKE', "%{$term}%");
            }
        }

        if ($query === '') $q->limit(500);

        return $q->orderByRaw("CONCAT(COALESCE(tp.last_name,''),tp.first_name)")->get()->toArray();
    }

    public function uploadDbReceipt(Request $request){
        if (!$request->has(['access_token','request','params'])) {
            Utils::errorResponse('badly formed request');
        }

        $this->checkToken($request->input('access_token'));

        if($request->input("request") == 'db_backup')

        $params = $request->input('params');
//        logger(print_r($params,true));

        DB::table("android_db_backup")->insert([
            "username"=>$params['username'],
            "install_id"=>$params['install_id'],
            "app_version"=>$params['app_version'],
            "db_version"=>$params['db_version'],
            "path"=>$params['path'],
            "filename"=>$params['filename'],
            "created_at"=>Utils::dateTimeStamp(),
        ]);

        Utils::successResponse("success");
    }

    public function uploadFile(Request $request){
        if (!$request->has('access_token') || !$request->has('request_type')) {
            Utils::errorResponse('badly formed request');
        }

        $this->checkToken($request->input('access_token'));

        $requestType = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->input('request_type'));
        $datePath = date('Y') . DIRECTORY_SEPARATOR . date('Y-m-d');
        $savedPath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $savedPath = $file->store("sync/{$requestType}/{$datePath}", 'local');
        } elseif ($request->has('file_content')) {
            $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $request->input('filename', 'upload'));
            $dir = storage_path("app/sync/{$requestType}/{$datePath}");
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            file_put_contents("{$dir}/{$filename}", $request->input('file_content'));
            $savedPath = "sync/{$requestType}/{$datePath}/{$filename}";
        }

        Utils::successResponse("success", ["path" => $savedPath ?? ""]);
    }

}
