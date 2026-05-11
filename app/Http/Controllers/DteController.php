<?php

namespace App\Http\Controllers;

use App\Common\Utils;
use App\DteConfig\Reporting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use App\DteConfig\Admin;
use App\DteConfig\Logging;

class DteController extends Controller
{
    protected $canViewAll = [99,98];
    protected $group = "";
    protected $id = "";

    protected $db;

    public function __construct()
    {
        $this->middleware('auth');
        $this->db = new DataTables\Database(Utils::getDbSettings());

        $this->middleware(function ($request, $next) {
            $this->group = Auth::user()->user_type_id;
            $this->id = Auth::user()->id;
            return $next($request);
        });
    }

    public function manageUsers(Admin $admin, Logging $logging){
        $admin->manageUsers($this->db,$this->group,$this->id,$logging)->process($_POST)->json();
    }

    public function mobilePasswordReset(Admin $admin, Logging $logging){
        $admin->mobilePasswordReset($this->db,$this->group,$this->id,$logging)->process($_POST)->json();
    }

    public function manageUsersScopeCustom(Admin $admin, Logging $logging){
        if ( ! isset($_POST['id']) ) exit( json_encode( Utils::drawNothing() ) );
        $admin->manageUsersScopeCustom($this->db,$this->group,$this->id,$logging)->process($_POST)->json();
    }

    public function manageUsersScopeGroup(Admin $admin, Logging $logging){
        if ( ! isset($_POST['id']) ) exit( json_encode( Utils::drawNothing() ) );
        $admin->manageUsersScopeGroup($this->db,$this->group,$this->id,$logging)->process($_POST)->json();
    }

    public function manageScopeGroups(Admin $admin, Logging $logging){
        $admin->manageScopeGroups($this->db,$this->group,$this->id,$logging)->process($_POST)->json();
    }

//    public function manageUsersScopeCustom(Admin $admin, Logging $logging){
//        if ( ! isset($_POST['id']) ) exit( json_encode( $this->drawNothing() ) );
//        $admin->manageUsersScopeCustom($this->db,$this->group,$this->id,$logging)->process($_POST)->json();
//    }

    public function manageSchools(Admin $admin, Logging $logging){
        $admin->manageSchools($this->db,$this->group,$this->id,$logging)->process($_POST)->json();
    }

    public function manageDistrictOffices(Admin $admin, Logging $logging){
        $manageDistrictOffices = $admin->manageDistrictOffices($this->db,$this->group,$this->id,$logging)->process($_POST)->json();
    }

    public function schoolMonitoring(Reporting $reporting, Logging $logging){
        $reporting->schoolMonitoring($this->db,$this->group,$this->id,$logging)->process($_POST)->json();
    }

}
