<?php

namespace App\Http\Controllers;

use App\Common\Queries\DbQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppDownloadController extends Controller
{
    //
    public function index()
    {
        $versionDetails = DbQueries::getLatestAndroidAppVersionQuery();
        return view('app_download',['versionDetails'=>$versionDetails]);
    }
}
