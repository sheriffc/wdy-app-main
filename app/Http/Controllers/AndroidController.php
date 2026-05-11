<?php

namespace App\Http\Controllers;

use App\Common\Queries\DbQueries;
use App\Common\Scripts\DeObfuscateStackTrace;
use App\Models\User;
use App\Common\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AndroidController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function remoteStackTrace(Request $request){

        $id = DB::table("android_trace")->insertGetId(
          ['as_json'=>json_encode($request->all())]
        );

        if(!$request->has(['stacktrace', 'version_code'])) exit();

        $ast = $request->all();

        $stackTrace = $request->input('stacktrace');
        $versionCode = $request->input('version_code');

        $stackTraceDecoded = DeObfuscateStackTrace::processStackTrace($stackTrace, $versionCode);

        if($stackTraceDecoded){
            //add it to the json object
            $ast['stacktrace_decoded'] = $stackTraceDecoded;
            DB::table('android_trace')->where(['id'=>$id])->update(['as_json'=>json_encode($ast)]);
        }
    }

    public function registration(Request $request){
        if (!$request->has(['registration'])) {
            //return error
            Utils::errorResponseLogin();
        }
        $registration = $request->input('registration');
        $registrationDecrypted = Utils::XorDecrypt($registration, env('XOR_KEY'));
        $registration = json_decode($registrationDecrypted,true);
        if(!isset($registration['username']) || !isset($registration['password'])){
            //return error
            Utils::errorResponseLogin("Login incorrect");
        }
        $usernameOrEmail = $registration['username'];
        $password = $registration['password'];
        $installId = $registration['install_id'];
        $clientTime = $registration['client_time'];

        //check if client time is within valid range
        $MAX_ALLOWED_CLIENT_TIME_INACCURACY = 5*60;
        $clientTimeInaccuracyInSeconds = strtotime($clientTime) - time();
        $clientTimeInaccuracyInMinutesAbsolute = (int) abs($clientTimeInaccuracyInSeconds/60);
        $clientTimeInaccuracyAheadOrBehind = ($clientTimeInaccuracyInSeconds > 0) ? "ahead" : "behind";
        if(abs($clientTimeInaccuracyInSeconds) > ($MAX_ALLOWED_CLIENT_TIME_INACCURACY)){
            Utils::errorResponseLogin("Your device date/time is incorrect by more than 5 minutes. \n" .
                "Please set correctly in Settings app then try again. \n" .
                "(Your device date/time is {$clientTimeInaccuracyAheadOrBehind} by {$clientTimeInaccuracyInMinutesAbsolute} minutes)"
            );
        }

        //check if valid
        if (Auth::once(['username' => $usernameOrEmail, 'password' => $password, 'active' => 1])  || Auth::once(['email' => $usernameOrEmail, 'password' => $password, 'active' => 1])) {

            $user = User::find(Auth::user()->id);

            //check if user has mobile access
            if(!$user->mobile_access) Utils::errorResponseLogin("Access to mobile app has not been granted, please contact your admin if you need permissions");
            if(!$user->scope_cache_id) Utils::errorResponseLogin("No school permissions have been assigned, please contact your admin if you need permissions");

            //for demo version
            if (Utils::isDemoEnv()) {
                if(empty($user->client_access_token)){
                    $token = Utils::createClientToken();
                    $user->client_access_token = $token;
                    $user->client_access_created_at = Utils::dateTimeStamp();
                }else{
                    $token = $user->client_access_token;
                }
            }else{
                $token = Utils::createClientToken();
                $user->client_access_token = $token;
                $user->client_access_created_at = Utils::dateTimeStamp();
            }
            $user->save();

            //on successful login, invalidate any resets for this install
            $affected = DB::table('android_password_resets')
                ->where('install_id', $installId)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled', 'updated_by' => 0, 'remarks' => 'any pending requests purged from successful login on this device']);

            $scopeSchools = DB::table('user_scope_cache')->where('id',$user->scope_cache_id)->value('school_uuids');
            if(!$scopeSchools) $scopeSchools = "";

            //generate a passphrase for the client database encryption if one doesn't already exist for install id
            $existingInstall = DB::table('android_install')->where('install_id', $installId)->first();
            if($existingInstall){
                $passphrase = $existingInstall->passphrase;
            }else{
                $passphrase = uniqid("wdysc");
                DB::table('android_install')->insert([
                    'install_id' => $installId,
                    'passphrase' => $passphrase,
                    'initiated_by' => $user->id,
                    'created_at' => Utils::dateTimeStamp(),
                    'updated_at' => Utils::dateTimeStamp()
                ]);
            }

            $dataPayload = [
                'access_token'=>$token,
                'username'=>$user->username,
                'user_id'=>$user->id,
                'name'=>$user->name,
                'scope_cache_id'=>$user->scope_cache_id,
                'scope_hash'=>$user->scope_hash,
                'scope_schools'=>$scopeSchools,
                'passphrase'=>$passphrase
            ];

            //api log
            DB::table("android_api_log")->insert([
                'user_action'=>'authentication successful',
                'json_data'=>json_encode(array_merge($dataPayload,['install_id'=>$installId, 'client_time'=>$clientTime]))
            ]);

            Utils::jsonHeader();
            exit( json_encode([
                'status'=>true,
                'message'=>'successfully authenticated',
                'data'=>Utils::XorEncrypt(json_encode($dataPayload),env('XOR_KEY'))
            ]));
        }else{
            Utils::errorResponseLogin("Login incorrect");
        }
        Utils::errorResponseLogin();
    }

    public function checkLatestAppVersion(Request $request){
        $userVersionCode = null;
        if ($request->has(['version_code']) ) {
            $userVersionCode = $request->version_code;
        }
        $latestVersionDetails = self::getLatestAppVersion($userVersionCode);
        Utils::successResponse("success",$latestVersionDetails);
    }

    public static function getLatestAppVersion($userVersionCode = null) {
        $latestVersionDetails = DbQueries::getLatestAndroidAppVersionQuery();
        if (empty($latestVersionDetails)) {
            return null;
        }

        $latestVersionDetails->force_update = 0;
        // if user submitted their current version code, check if they should be forced to update
        if ($userVersionCode) {
            $forceUpdate = DbQueries::checkForceUpdateByAndroidAppVersionQuery($userVersionCode);
            if (isset($forceUpdate->force_update)) {
                $latestVersionDetails->force_update = $forceUpdate->force_update;
            }
        }
        return $latestVersionDetails;
    }
}
