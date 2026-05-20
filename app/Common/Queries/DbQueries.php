<?php
namespace App\Common\Queries;

use Illuminate\Support\Facades\DB;
use PDO;

class DbQueries {

    public static function getLatestAndroidAppVersionQuery(){
        return DB::table('app_version')
            ->select('version_code', 'version_name', 'filename', 'description','uri')
            ->where('active', '=', 1)
            ->orderBy('version_code', 'desc')->first();
    }

    public static function checkForceUpdateByAndroidAppVersionQuery($userVersionCode) {
        return DB::table('app_version')
            ->select('force_update')
            ->where('version_code', '=', $userVersionCode)
            ->orderBy('version_code', 'desc')->first();
    }

    public static function getActiveDistricts(){
        $sql = "
            SELECT
                do2.name,
                g.id AS district_id
            FROM district_office do2
            JOIN geo g ON g.name = do2.name AND g.type = 2
            WHERE do2.active = true
            ORDER BY do2.name
        ";
        return DB::select($sql);
    }

    public static function getDistrictChiefdoms($districtId){
        $sql ="
            SELECT 
                id,
                name 
            FROM geo 
            WHERE parent_id = ?
                AND type = 3
                AND active = true
            ORDER BY name;
        ";
        return DB::select($sql,[$districtId]);
    }

    public static function getOptionList($listName){
        $sql="
        SELECT 
            id,
            item_name,
            item_id
        FROM option_list
        WHERE list_name = ?
            AND active = true;
        ";
        return DB::select($sql,[$listName]);
    }
}
