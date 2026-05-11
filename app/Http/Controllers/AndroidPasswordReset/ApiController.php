<?php

namespace App\Http\Controllers\AndroidPasswordReset;

use App\Api\DataSync;
use App\Api\PasswordResetQueries;
use App\Common\Queries\DbQueries;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Common\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function checkStatus(Request $request) {
        // get status of requests for the posted install_id
        $payload = $this->handleRegistrationRequest($request);
        if(!isset($payload['install_id'])){
            //return error
            Utils::errorResponse('badly formed request');
        }
        //check if there are any entries for this install_id
        $responsePayload = PasswordResetQueries::passwordRequestStatus($payload['install_id']);
        Utils::successResponse("success",$responsePayload);
    }

    public function requestPasswordChange(Request $request) {
        $payload = $this->handleRegistrationRequest($request);
        if(!isset($payload['install_id']) && !isset($payload['username']) && !isset($payload['password'])){
            //return error
            Utils::errorResponse('badly formed request');
        }
        //check if the username exists
        $user = User::where('username',$payload['username'])
            ->where('mobile_access',1)
            ->where('active',1)
            ->first();

        if(!$user) Utils::errorResponse("This username does not exist (or is deactivated)");

        DB::table("android_password_resets")->insert([
            "username"=>$payload['username'],
            "install_id"=>$payload['install_id'],
            "proposed_new_password_hash"=>Hash::make($payload['password']),
            "status"=>'pending',
            "remarks"=>null,
            "created_at"=> Utils::dateTimeStamp(),
            "expires_at"=> Utils::dateTimeStamp(time() + (60 * 60 * 24 * 7)), // 7 days
            "updated_at"=> Utils::dateTimeStamp(),
            "updated_by"=>0 // 0 for machine
        ]);
        $responsePayload = PasswordResetQueries::passwordRequestStatus($payload['install_id']);
        Utils::successResponse("success",$responsePayload);
        //todo: send notification to appointed DO
    }

    public function cancelRequest(Request $request) {
        // cancel an initiated request for a password reset
        $payload = $this->handleRegistrationRequest($request);
        if(!isset($payload['install_id']) && !isset($payload['id']) && !isset($payload['username'])){
            //return error
            Utils::errorResponse('badly formed request');
        }
        //update record
        $affected = DB::table('android_password_resets')
            ->where('id', $payload['id'])
            ->update(['status' => 'cancelled', 'updated_by' => 0, 'remarks' => 'cancelled from device']);

        if($affected){
            //return updated status in payroll
            $responsePayload = PasswordResetQueries::passwordRequestStatus($payload['install_id']);
            Utils::successResponse("success",$responsePayload);
        }else{
            Utils::errorResponse("failed");
        }
    }

    public function handleRegistrationRequest(Request $request){
        if (!$request->has(['registration'])) {
            //return error
            Utils::errorResponse('badly formed request');
        }
        $registration = $request->input('registration');
        $registrationDecrypted = Utils::XorDecrypt($registration, env('XOR_KEY'));
        return json_decode($registrationDecrypted,true);
    }

    public function approveReset(Request $request){
        $userId = Auth::user()->id;
        $id = $request->input("id");
        $remarks = $request->input("remarks");

        $userName = DB::table('android_password_resets')->where('id', $id)->value('username');
        $newPassword = DB::table('android_password_resets')->where('id', $id)->value('proposed_new_password_hash');
        $oldPassword = DB::table('user')->where('username', $userName)->value('password');

        $affected = DB::table('android_password_resets')
            ->where('id', $id)
            ->update(['status' => 'approved', 'updated_by' => $userId, 'remarks' => $remarks, 'old_password_hash'=>$oldPassword]);

        //update password
        $affectedUser = DB::table('user')
            ->where('username', $userName)
            ->update(['password' => $newPassword]);

        if($affected && $affectedUser){
            Utils::successResponse("success");
        }else{
            Utils::errorResponse("failed");
        }
    }
    public function rejectReset(Request $request){
        $userId = Auth::user()->id;
        $id = $request->input("id");
        $remarks = $request->input("remarks");
        $affected = DB::table('android_password_resets')
            ->where('id', $id)
            ->update(['status' => 'rejected', 'updated_by' => $userId, 'remarks' => $remarks]);

        //update password

        if($affected){
            Utils::successResponse("success");
        }else{
            Utils::errorResponse("failed");
        }
    }

}
