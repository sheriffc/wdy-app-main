<?php

namespace App\Http\Controllers;

use App\Common\Utils;
use App\DteConfig\Reporting;
use DataTables;

class PublicDteController extends Controller
{
    protected $db;
    public function __construct()
    {
        $this->db = new DataTables\Database(Utils::getDbSettings());
    }
    public function schoolList(Reporting $reporting){
       if(isset($_POST['districtId']) && !empty($_POST['districtId'])){
        $reporting->schoolMonitoringByDistrictId($this->db,false ,false, false,$_POST['districtId'])->process($_POST)->json();
       }else{
        $reporting->schoolMonitoring($this->db,false ,false, false)->process($_POST)->json();
       }
       
    }

}
