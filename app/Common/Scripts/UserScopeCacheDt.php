<?php

namespace App\Common\Scripts;

use Illuminate\Support\Facades\DB;
use PDO;

/** generates and caches any user permissions for schools if not present */

class UserScopeCacheDt {
    public static function run($db=false)
    {
        //check user table for any empty hashes
        if($db){
            $users = $db->sql("SELECT * FROM user WHERE scope_hash IS NULL OR scope_hash = ''")->fetchAll(PDO::FETCH_CLASS);
        }else {
            $users = DB::select("SELECT * FROM user WHERE scope_hash IS NULL OR scope_hash = ''");
        }

        foreach ($users as $user){

            $schoolIdsString = self::getSchoolIdsForUserAsString($user->id,$db);

            if($schoolIdsString){
                $hash = md5($schoolIdsString);
                //check if hash already exists in cache
                if($db){
                    $cacheRec = $db->sql("SELECT id FROM user_scope_cache WHERE hash='$hash'")->fetch();
                    if($cacheRec){
                        $cacheId = $cacheRec['id'];
                    }else{
                        $cacheId = $cacheRec;
                    }

                }else{
                    $cacheId = DB::scalar("SELECT id FROM user_scope_cache WHERE hash=:hash",['hash'=>$hash]);
                }
                if(!$cacheId){
                    if($db){
                        $cacheId = $db->insert("user_scope_cache", ["hash"=>$hash,'school_uuids'=>$schoolIdsString])->insertId();
                    }else{
                        $cacheId = DB::table('user_scope_cache')->insertGetId([
                            'hash'=>$hash,
                            'school_uuids'=>$schoolIdsString,
                        ]);
                    }
                }

                if($db){
                    $db->update('user',['scope_cache_id'=>$cacheId,'scope_hash'=>$hash],['id'=>$user->id]);
                }else{
                    DB::table('user')
                        ->where('id', $user->id)
                        ->update(['scope_cache_id'=>$cacheId,'scope_hash'=>$hash]);
                }

            }
        }
    }

    public static function getSchoolIdsForUser($userId,$db=false){
        $customSchoolIds = self::getCustomPermissions($userId);
        $groupsSchoolIds = self::getGroupPermissions($userId);
        $schoolIds = array_unique(array_merge($customSchoolIds,$groupsSchoolIds));
        sort($schoolIds);
        return $schoolIds;
    }

    public static function getSchoolIdsForUserAsString($userId,$db=false){
        $schoolIds = self::getSchoolIdsForUser($userId);
        if($schoolIds){
            return implode(',',$schoolIds);
        }else{
            return '';
        }
    }

    public static function getSchoolIdsForUserAsHash($userId,$db=false){
        $schoolIdsString = self::getSchoolIdsForUserAsString($userId);
        if($schoolIdsString){
            return md5($schoolIdsString);
        }else{
            return false;
        }
    }

    /** input: user id, output: array of school IDs  */
    public static function getCustomPermissions($userId,$db=false){
        if($db){

        }else {
            $schoolsAssigned = DB::select("SELECT * FROM user_scope_custom_assignment WHERE user_id = :id", ['id' => $userId]);
        }
        if(!$schoolsAssigned){
            return [];
        }
        $schoolIds = [];
        foreach($schoolsAssigned as $school){
            $schoolIds[] = $school->school_uuid;
        }
        return $schoolIds;
    }

    /** input: user id, output: array of school IDs  */
    public static function getGroupPermissions($userId,$db=false){
        $delimiter = ',';
        if($db){
            $groupsAssigned = $db->sql("SELECT * FROM user_scope_group_user_link lnk INNER JOIN user_scope_group grp ON lnk.group_id = grp.id WHERE user_id = '$userId' AND grp.active")->fetchAll(PDO::FETCH_CLASS);
        }else {
            $groupsAssigned = DB::select("SELECT * FROM user_scope_group_user_link lnk INNER JOIN user_scope_group grp ON lnk.group_id = grp.id WHERE user_id = :id AND grp.active", ['id' => $userId]);
        }
        if(!$groupsAssigned){
            return [];
        }

        $schoolIds = [];
        //get and aggregate groups
        foreach($groupsAssigned as $group){
            if($group->district_selection){
                $districtIds = explode($delimiter, $group->district_selection);
                foreach($districtIds as $districtId){
                    if($db){
                        $schools = $db->sql("SELECT uuid FROM school WHERE district_office_uuid='$districtId'")->fetchAll(PDO::FETCH_CLASS);
                    }else {
                        $schools = DB::select("SELECT uuid FROM school WHERE district_office_uuid=:id", ['id' => $districtId]);
                    }
                    foreach($schools as $school){
                        $schoolIds[] = $school->uuid;
                    }
                }
            }

            if($group->school_selection){
                $schoolIds[] = explode($delimiter, $group->school_selection);
            }
        }

        return $schoolIds;
    }
}
