<?php

namespace App\Console\Commands;

use App\Common\Utils;
use App\Mail\AndroidStackTraceReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;

class SendAndroidStackTraceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:androidstacktracereport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $isSuccessful = true;
        //get receipients
        $recipientsString = env("TRACE_REPORT_MAIL_RECIPIENTS","");
        $recipients = explode(",",$recipientsString);

        if(!$recipients) return Command::FAILURE;

        $stateJsonPath = storage_path('app/state.json');
        $keyName = 'ast_last_checked';

        $lastChecked = Utils::dateTimeStamp(time() - 60*60*24);
        $timeNow = Utils::dateTimeStamp();

//        $buildType = env("ANDROID_BUILD_TYPE", "");
//        $appUrl = env("APP_URL", "");

        if(file_exists($stateJsonPath)){
            $stateJson = file_get_contents($stateJsonPath);
            $state = json_decode($stateJson, true);
            if(isset($state[$keyName])){
                $lastChecked = $state[$keyName];
            }
        }

        $sql = "
        SELECT
        t.id,
        t.created_at,
        u.id user_id,
        u.username,
        -- version_number was renamed to version_code for consistency, but need to support old app submissions
        COALESCE(t.as_json->>'$.version_number',t.as_json->>'$.version_code') version_code,
        t.as_json->>'$.stacktrace' stack_trace,
        t.as_json->>'$.stacktrace_decoded' stack_trace_decoded
        FROM android_trace t
        INNER JOIN user u ON u.id = JSON_UNQUOTE(JSON_EXTRACT(t.as_json, '$.user_id'))
        WHERE t.created_at BETWEEN '$lastChecked' AND '$timeNow'
        ORDER BY t.created_at DESC
        ";

        $res = DB::select($sql);

        if($res){

            foreach($recipients as $recipient) {
                try {
                    Mail::to(trim($recipient))->send(new AndroidStackTraceReport($res, $lastChecked, $timeNow));
                } catch (Exception $e) {
                    logger("error emailing $recipient ".$e->getMessage());
                    $isSuccessful = false;
                }
            }
        }

        if($isSuccessful){
            file_put_contents($stateJsonPath, json_encode([$keyName=>Utils::dateTimeStamp()]));
            return Command::SUCCESS;
        }else{
            return Command::FAILURE;
        }

    }
}
