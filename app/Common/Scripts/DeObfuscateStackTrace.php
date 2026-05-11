<?php

namespace App\Common\Scripts;

use Illuminate\Support\Facades\DB;

class DeObfuscateStackTrace
{

    public static function processStackTraces(): void{
        $sql = "
        SELECT
            *
        FROM android_trace t
        WHERE t.as_json->>'$.version_code'>=13
            AND t.as_json->>'$.stacktrace_decoded' IS NULL
            -- a few spurious records that somehow have 'null' stacktrace cause issues so ignore them
            AND t.as_json->>'$.stacktrace' <> 'null'
        ";
        $recs = DB::select($sql);
        $counter = 0;
        if($recs){
            foreach($recs as $rec){
                $json = $rec->as_json;
                $ast = json_decode($json);
                $stackTrace = $ast->stacktrace;
                $versionCode = $ast->version_code;
                $stackTraceDecoded = self::processStackTrace($stackTrace, $versionCode);
                if($stackTraceDecoded){
                    //add it to the json object
                    $ast->stacktrace_decoded = $stackTraceDecoded;
                    DB::table('android_trace')->where(['id'=>$rec->id])->update(['as_json'=>json_encode($ast)]);
                    echo "stack trace id: ".$rec->id." decoded".PHP_EOL;
                    $counter++;
                }
            }
        }
        if($counter){
            echo "$counter stack traces processed".PHP_EOL;
        }else{
            echo "no stack traces processed".PHP_EOL;
        }
    }

    public static function processStackTrace($stackTrace, $versionCode): String {
        //check retrace exists
        $retracePath = env('RETRACE_PATH','');

        if(empty($retracePath)) return "";

        //check mapping exists
        $mappingPath = storage_path("mapping/$versionCode/mapping.txt");

        if(!file_exists($mappingPath)) return "";

        $uniqueId = uniqid('ast_').".txt";

        $astPath = storage_path("temp/$uniqueId");

        $tmpPath = storage_path('temp');
        if (!file_exists($tmpPath)) {
            mkdir($tmpPath, 0777, true);
        }

        file_put_contents($astPath, $stackTrace);

        $commandToRun = "$retracePath $mappingPath $astPath";

        $output = shell_exec($commandToRun);

        unlink($astPath);

        return $output;
    }

}
