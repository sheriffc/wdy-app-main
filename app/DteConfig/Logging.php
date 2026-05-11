<?php

namespace App\DteConfig;

use Illuminate\Support\Facades\Auth;

class Logging
{
    protected $pkLookup = [
        "users"=>"id",
        "person"=>"idx",
        "record"=>"idx",
    ];
    protected $uid;

    public function __construct(){
        $this->uid = Auth::user()->id;
    }

    public function preEdit(){
        $pkLookup = $this->pkLookup;
        $uid = $this->uid;
        return function ($e,$id,$values) use ($pkLookup,$uid){
            /** log event */
            $keys = array_keys($values);
            $table = $keys[0];
            $arrIns = [
                "tbl"=>$table,
                "user"=>$uid,
                "pk"=>$id,
                "op"=>"e",
                "ts"=>now()
            ];
            $ref_id = $e->db()->insert("log_event", $arrIns)->insertId();
            $lookupField = $pkLookup[$table];
            /** get from */
            $sql = "SELECT * FROM $table WHERE $lookupField = '$id'";
            $from = $e->db()->sql($sql)->fetch();
            /** log edits */
            foreach ($values[$table] as $field=>$to){
                if ($from[$field] !== $to) {
                    $arrIns = [
                        "log_ref" => $ref_id,
                        "field" => $field,
                        "val_fr" => $from[$field],
                        "val_to" => $to
                    ];
                    $e->db()->insert("log_edit", $arrIns);
                }
            }
        };
    }
    public function postCreate(){
        $uid = $this->uid;
        return function ($e,$id,$values,$row) use ($uid){
            /** log event */
            $keys = array_keys($values);
            $table = $keys[0];
            $arrIns = [
                "tbl"=>$table,
                "user"=>$uid,
                "pk"=>$id,
                "op"=>"c",
                "ts"=>now()
            ];
            $ref_id = $e->db()->insert("log_event", $arrIns)->insertId();
            /** log create */
            $arrIns = [
                "log_ref"=>$ref_id,
                "json"=>json_encode($row)
            ];
            $e->db()->insert("log_create_delete", $arrIns);
        };
    }
}
