<?php

namespace App\Common;

class Utils
{
    public static function getDbSettings(){
        $default = config('database.default');
        return [
            "type" => ucfirst($default),     // Database type: "Mysql", "Postgres", "Sqlserver", "Sqlite" or "Oracle"
            "user" => config("database.connections.$default.username"),          // Database user name
            "pass" => config("database.connections.$default.password"),          // Database password
            "host" => config("database.connections.$default.host"), // Database host
            "port" => config("database.connections.$default.port"),          // Database connection port (can be left empty for default)
            "db"   => config("database.connections.$default.database"),          // Database name
            "dsn"  => "charset=utf8mb4",          // PHP DSN extra information. Set as `charset=utf8mb4` if you are using MySQL
            "pdoAttr" => array()   // PHP PDO attributes array. See the PHP documentation for all options
        ];
    }

    function checkInput($array){
        foreach($array as $item){
            if(empty($item)) exit(json_encode( $this->drawNothing() ));
        }
    }

    public static function drawNothing(){
        return [
            "data" => []
        ];
    }
    public static function XorEncrypt($message, $key): String{
        $ml = strlen($message);
        $kl = strlen($key);
        $newmsg = "";

        for ($i = 0; $i < $ml; $i++){
            $newmsg = $newmsg . ($message[$i] ^ $key[$i % $kl]);
        }

        return base64_encode($newmsg);
    }

    public static function XorDecrypt($encrypted_message, $key): String{
        $msg = base64_decode($encrypted_message);
        $ml = strlen($msg);
        $kl = strlen($key);
        $newmsg = "";

        for ($i = 0; $i < $ml; $i++){
            $newmsg = $newmsg . ($msg[$i] ^ $key[$i % $kl]);
        }
        return $newmsg;
    }

    public static function dateTimeStamp($time=false): String{
        if(!$time) $time = time();
        return date("Y-m-d H:i:s", $time);
    }

    public static function createClientToken(): String{
        return uniqid('wdy');
    }

    public static function jsonHeader(): void{
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
    }

    public static function errorResponse($message=false): void{
        self::jsonHeader();
        if(!$message) $message = "error occurred, try again";
        exit(json_encode(['status'=>false,'message'=>$message, 'data'=>[]]));
    }

    public static function errorResponseLogin($message=false): void{
        self::jsonHeader();
        if(!$message) $message = "error occurred, try again";
        exit(json_encode(['status'=>false,'message'=>$message, 'data'=>""]));
    }

    public static function successResponse($message=false,$data=[]): void{
        self::jsonHeader();
        if(!$message) $message = "success";

        $responseJson = json_encode(['status'=>true,'message'=>$message, 'data'=>$data]);
//        logger($responseJson);
        exit($responseJson);
    }

    public static function isDemoEnv(): bool {
        return (env('APP_ENV') == "demo" && str_contains(env('APP_URL'),'demo.'));
    }
}
