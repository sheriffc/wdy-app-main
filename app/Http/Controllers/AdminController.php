<?php

namespace App\Http\Controllers;

use App\Queries\Admin;
use App\Services\TsctmisService;
use App\Common\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    protected $adminTypes = [999,98];
    protected $userType = "";
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

//        $this->middleware(function ($request, $next) {
//            $this->userType = Auth::user()->user_type_id;
//            $this->id = Auth::user()->id;
//            return $next($request);
//        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::user()->user_type_id > 0){
            if(Session::has("lastVisitedPage")){
                return redirect(Session::get("lastVisitedPage"));
            }
            return redirect('/');
        }
        return view('dashboard');
    }

    public function manageUsers()
    {
        return view('admin.users');
    }

    public function mobilePasswordReset()
    {
        return view('admin.mobile-password-reset');
    }

    public function manageScopeGroups()
    {
        return view('admin.scope-groups');
    }

    public function manageSchools()
    {
        return view('admin.schools');
    }

    public function manageDistrictOffices()
    {
        return view('admin.district-offices');
    }

    public function viewTrace(Admin $admin)
    {
        return view('admin.trace',[
            "records"=>$admin->viewLatestTraceReports(100)
        ]);
    }

    public function manageAcademicYears()
    {
        return view('admin.academic-years');
    }

    public function listAcademicYears()
    {
        $years = DB::table('school_academic_year')
            ->whereNull('deleted_at')
            ->orderByDesc('academic_year')
            ->get(['uuid', 'academic_year_name', 'academic_year', 'date_from', 'date_to', 'active']);

        return response()->json($years);
    }

    public function saveAcademicYear(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|integer|min:2000|max:2100',
            'date_from'     => 'required|date',
            'date_to'       => 'required|date|after:date_from',
            'set_active'    => 'nullable|integer|in:0,1',
        ]);

        $year     = (int) $request->academic_year;
        $yearName = $year . '/' . ($year + 1);
        $now      = Utils::dateTimeStamp();
        $uuid     = $request->uuid ?: (string) Str::orderedUuid();
        $isNew    = empty($request->uuid);

        if ($isNew) {
            $exists = DB::table('school_academic_year')
                ->where('academic_year', $year)
                ->whereNull('deleted_at')
                ->exists();
            if ($exists) {
                return response()->json(['status' => false, 'message' => "Academic year $yearName already exists."]);
            }
        }

        $record = [
            'academic_year_name' => $yearName,
            'academic_year'      => $year,
            'date_from'          => $request->date_from,
            'date_to'            => $request->date_to,
            'active'             => 0,
            'updated_at'         => $now,
            'synced_at'          => $now,
        ];

        if ($isNew) {
            $record['uuid']       = $uuid;
            $record['created_at'] = $now;
            DB::table('school_academic_year')->insert($record);
        } else {
            DB::table('school_academic_year')
                ->where('uuid', $uuid)
                ->update($record);
        }

        if ($request->set_active) {
            DB::table('school_academic_year')
                ->whereNull('deleted_at')
                ->where('uuid', '<>', $uuid)
                ->update(['active' => 0, 'synced_at' => $now, 'updated_at' => $now]);

            DB::table('school_academic_year')
                ->where('uuid', $uuid)
                ->update(['active' => 1, 'synced_at' => $now, 'updated_at' => $now]);
        }

        return response()->json(['status' => true]);
    }

    public function activateAcademicYear(Request $request)
    {
        $request->validate(['uuid' => 'required|string']);

        $now = Utils::dateTimeStamp();

        $exists = DB::table('school_academic_year')
            ->where('uuid', $request->uuid)
            ->whereNull('deleted_at')
            ->exists();

        if (!$exists) {
            return response()->json(['status' => false, 'message' => 'Academic year not found.']);
        }

        DB::table('school_academic_year')
            ->whereNull('deleted_at')
            ->update(['active' => 0, 'synced_at' => $now, 'updated_at' => $now]);

        DB::table('school_academic_year')
            ->where('uuid', $request->uuid)
            ->update(['active' => 1, 'synced_at' => $now, 'updated_at' => $now]);

        return response()->json(['status' => true]);
    }

    public function syncTsctmisTeachers(TsctmisService $tsctmis)
    {
        try {
            $result = $tsctmis->syncNonPayrollTeachers();
            return response()->json([
                'status'  => true,
                'message' => "Sync complete: {$result['inserted']} inserted, {$result['updated']} updated ({$result['total']} total non-payroll teachers from TSCTMIS).",
                'data'    => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 502);
        }
    }

}
