<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\PublicController::class, 'landing'])->name('landing');

Route::post('/api/dte/dt-school-list', [App\Http\Controllers\PublicDteController::class, 'schoolList'])->name('dte.reporting.school-list');

Route::post('/api/active-districts', [App\Http\Controllers\PublicController::class, 'getActiveDistricts'])->name('active-districts');
Route::post('/api/academic-years-filter', [App\Http\Controllers\PublicController::class, 'getAcademicYearsForFilter'])->name('academic-years-filter');

Route::post('/api/district-chiefdoms', [App\Http\Controllers\PublicController::class, 'getDistrictChiefdoms'])->name('district-chiefdoms');

Route::post('/api/attendance-dates', [App\Http\Controllers\PublicController::class, 'getLandingDropdownDates'])->name('attendance-dates');

Route::post('/api/teacher-attendance', [App\Http\Controllers\PublicController::class, 'getLandingData'])->name('teacher-attendance');
//
//Route::post('/api/attendance-map', [App\Http\Controllers\PublicController::class, 'getLandingData'])->name('attendance-map');

Route::get('/app-download', [App\Http\Controllers\AppDownloadController::class, 'index'])->name('app-download');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/faqs', function () {
    return view('faqs');
})->name('faqs');

Route::get('/report-monitoring',[App\Http\Controllers\PublicController::class, 'reportMonitoring'])->name('report-monitoring');

Route::post('/api/daily-school-report-data',[App\Http\Controllers\PublicController::class, 'getSchoolDailyReportChartData'])->name('daily-school-report-data-api');

Route::post('/api/class-gender-ratio-data',[App\Http\Controllers\PublicController::class, 'getclassGenderRatio'])->name('class-gender-ratio-data-api');

Route::post('/api/maternal-status-chart',[App\Http\Controllers\PublicController::class, 'getMaternalStatusChart'])->name('maternal-status-chart-api');

Route::post('/api/maternal-status-geo-location',[App\Http\Controllers\PublicController::class, 'getMaternalStatusGeoLocation'])->name('maternal-status-geo-location-api');

Route::post('/api/learner-absenteeism-rates',[App\Http\Controllers\PublicController::class, 'getLearnerAbsenteeismRates'])->name('learner-absenteeism-rates-api');

Route::post('/api/learners-special-needs',[App\Http\Controllers\PublicController::class, 'getSpecialNeedsChart'])->name('learners-special-needs-api');

Route::post('/api/learners-special-needs-absteeism-data',[App\Http\Controllers\PublicController::class, 'getSpecialNeedsAttendanceAbsenteeismChart'])->name('learners-special-needs-absteeism-data-api');

Route::post('/api/learners-removed-data',[App\Http\Controllers\PublicController::class, 'getRemovedLearnersChart'])->name('learners-removed-data-api');

Route::post('/api/learners-at-risk-data',[App\Http\Controllers\PublicController::class, 'geAtRiskLearnersChart'])->name('learners-at-risk-data-api');

Route::post('/api/learners-at-risk-school-data',[App\Http\Controllers\PublicController::class, 'geAtRiskSchoolsChart'])->name('learners-at-risk-school-data-api');

Route::post('/api/gender-severity-absenteeism-rate',[App\Http\Controllers\PublicController::class, 'getGenderSeverityChart'])->name('gender-severity-absenteeism-rate-data-api');

Route::post('/api/district-absenteeism-rate',[App\Http\Controllers\PublicController::class, 'getDistrictAbsenteeismChart'])->name('district-absenteeism-rate-data-api');

Route::post('/api/age-absenteeism-rate',[App\Http\Controllers\PublicController::class, 'getAgeAbsenteeismChart'])->name('age-absenteeism-rate-data-api');

Route::post('/api/absenteeism-distribution-chart',[App\Http\Controllers\PublicController::class, 'getAbsenteeismDistributionChart'])->name('absenteeism-distribution-chart-data-api');

Route::post('/api/trends-over-time-chart',[App\Http\Controllers\PublicController::class, 'getTrendsOverTimeChart'])->name('trends-over-time-chart-data-api');

Route::get('/school-list',[App\Http\Controllers\PublicController::class, 'schoolList'])->name('school-list');

Route::get('/school/{uuid}',[App\Http\Controllers\PublicController::class, 'schoolProfile'])->name('school-profile');

Route::post('/api/school/{uuid}',[App\Http\Controllers\PublicController::class, 'getSchoolProfileData'])->name('school-profile-api');

Route::post('/api/school/attendance-dates/{uuid}',[App\Http\Controllers\PublicController::class, 'getSchoolProfileDropdownDates'])->name('school-dates-api');

Route::get('/school-attendance-monitoring',[App\Http\Controllers\PublicController::class, 'schoolAttendanceMonitoring'])->name('school-attendance-monitoring');

Route::post('/api/school-attendance-monitoring-data',[App\Http\Controllers\PublicController::class, 'getSchoolAttendanceMonitoringData'])->name('school-attendance-monitoring-api');

Route::get('/teacher-reports',[App\Http\Controllers\PublicController::class, 'teacherReports'])->name('teacher-reports');

Route::post('/api/teacher-reports/teacher-sanctions',[App\Http\Controllers\PublicController::class, 'getTeachersEligbleForSanctionsTable'])->name('teacher-reports/teacher-sanctions-api');

Route::post('/api/teacher-reports/unauthorised-transfers',[App\Http\Controllers\PublicController::class, 'getUnauthorisedTeacherTransfersTable'])->name('teacher-reports/unauthorised-transfers-api');

Route::post('/api/teacher-reports/removed-teachers',[App\Http\Controllers\PublicController::class, 'getRemovableTeachersTable'])->name('teacher-reports/removed-teachers-api');

Route::post('/api/teacher-reports/further-investigation',[App\Http\Controllers\PublicController::class, 'getTeacherRequiringInvestigationTable'])->name('teacher-reports/further-investigation-api');

Route::post('/api/teacher-reports/active-teachers',[App\Http\Controllers\PublicController::class, 'getActiveTeachersTable'])->name('teacher-reports/active-teachers-api');

Route::get('/learner-reports',[App\Http\Controllers\PublicController::class, 'learnerReports'])->name('learner-reports');

Route::post('/api/learner-reports/at-risk-learner-data',[App\Http\Controllers\PublicController::class, 'getAtRiskLearnersTable'])->name('learner-reports/at-risk-table-api');

Route::post('/api/learner-reports/disability-table',[App\Http\Controllers\PublicController::class, 'getDisabilityLearnersTable'])->name('learner-reports/disability-table-api');

Route::post('/api/learner-reports/unassigned-table',[App\Http\Controllers\PublicController::class, 'getUnassignedLearnersTable'])->name('learner-reports/unassigned-table-api');

Route::post('/api/learner-reports/duplicate-enrollment-table',[App\Http\Controllers\PublicController::class, 'getDuplicateEnrollmentTable'])->name('learner-reports/duplicate-enrollment-table-api');

Route::get('/teacher-profile/{uuid}',[App\Http\Controllers\PublicController::class, 'teacherProfile'])->name('teacher-profile');

Route::post('/api/teacher-profile/school-data/{uuid}',[App\Http\Controllers\PublicController::class, 'getTeacherSchoolData'])->name('teacher-profile/schoold-data-api');

Route::post('/api/teacher-profile/assigned-learners/{uuid}',[App\Http\Controllers\PublicController::class, 'getTeacherAssignedLearners'])->name('teacher-profile/assigned-learners-api');

Route::post('/api/teacher-profile/attendance-summary/{uuid}',[App\Http\Controllers\PublicController::class, 'getTeacherAttendanceSummaryData'])->name('teacher-profile/attendance-summary-api');

Route::post('/api/teacher-profile/attendance-records/{uuid}',[App\Http\Controllers\PublicController::class, 'getTeacherAttendanceRecordsData'])->name('teacher-profile/attendance-records-api');

Route::post('/api/teacher-profile/time-table/{uuid}',[App\Http\Controllers\PublicController::class, 'getTeacherTimeTableData'])->name('teacher-profile/time-table-api');

Route::post('/api/teacher-absent-reasons',[App\Http\Controllers\PublicController::class, 'getTeacherAbsentReasons'])->name('teacher-absent-reasons-api');

Route::get('/learner-profile/{uuid}',[App\Http\Controllers\PublicController::class, 'learnerProfile'])->name('learner-profile');

Route::post('/api/learner-profile/attendance-records/{uuid}',[App\Http\Controllers\PublicController::class, 'getLearnerAttendanceRecords'])->name('learner-profile/attendance-records-api');



Auth::routes();

// Route::get('/profile-completion/teachers', function () {
//     return view('profile_completion.teachers');
// })->name('teachers-profile-completion');

Route::post('/api/profile-completion',[App\Http\Controllers\PublicController::class, 'getSchoolProfileCompletionTable'])->name('profile-completion-api');

// Route::get('/profile-completion/learners', function () {
//     return view('profile_completion.learners');
// })->name('learners-profile-completion');

// Route::post('/api/profile-completion/learners',[App\Http\Controllers\PublicController::class, 'getSchoolLearnersProfileCompletionTable'])->name('learners-profile-completion-api');

//Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//        $request->fulfill();
//        return redirect('/dashboard');
//    })->middleware(['signed'])->name('verification.verify');

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.resend');

Route::middleware(['auth'])->group(function () {

    Route::get('/email/verify', function () {
        return view('auth.verify');
    })->name('verification.notice');

    Route::middleware(['verified'])->group(function () {

        Route::get('/review-status', function () {
            return view('auth.review-status');
        })->name('review-status');

        Route::get('/review-status-alt', function () {
            return view('auth.review-status-no-email');
        })->name('review-status-no-email');

        Route::middleware(['review'])->group(function () {
           Route::get('/home', [App\Http\Controllers\AdminController::class, 'index'])->name('home');
           Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
        });

        Route::get('/admin/learners', [App\Http\Controllers\LearnerAdminController::class, 'index'])->name('manage.learners');
        Route::get('/admin/learners/list', [App\Http\Controllers\LearnerAdminController::class, 'listJson'])->name('manage.learners.list');

        Route::get('/admin/users', [App\Http\Controllers\AdminController::class, 'manageUsers'])->name('manage.users');
        Route::post('/api/dte/dt-users', [App\Http\Controllers\DteController::class, 'manageUsers'])->name('dte.manage.users');

        Route::post('/api/dte/dt-users-scope-custom', [App\Http\Controllers\DteController::class, 'manageUsersScopeCustom'])->name('dte.manage.users-scope-custom');
        Route::post('/api/dte/dt-users-scope-group', [App\Http\Controllers\DteController::class, 'manageUsersScopeGroup'])->name('dte.manage.users-scope-group');

        //password resets

        Route::get('/admin/mobile-password-reset', [App\Http\Controllers\AdminController::class, 'mobilePasswordReset'])->name('manage.mobile-password-reset');
        Route::post('/api/dte/dt-mobile-password-reset', [App\Http\Controllers\DteController::class, 'mobilePasswordReset'])->name('dte.manage.mobile-password-reset');
        Route::post('/admin/password-reset/approve-reset', [App\Http\Controllers\AndroidPasswordReset\ApiController::class, 'approveReset']);
        Route::post('/admin/password-reset/reject-reset', [App\Http\Controllers\AndroidPasswordReset\ApiController::class, 'rejectReset']);

        Route::middleware(['admin'])->group(function () {
            //stack traces
            Route::get('/admin/trace', [App\Http\Controllers\AdminController::class, 'viewTrace'])->name('view.trace');

            // TSCTMIS integration
            Route::post('/api/admin/sync-tsctmis-teachers', [App\Http\Controllers\AdminController::class, 'syncTsctmisTeachers'])->name('admin.sync-tsctmis-teachers');

            //dte
            Route::get('/admin/scope-groups', [App\Http\Controllers\AdminController::class, 'manageScopeGroups'])->name('manage.scope-groups');
            Route::post('/api/dte/dt-scope-groups', [App\Http\Controllers\DteController::class, 'manageScopeGroups'])->name('dte.manage.scope-groups');

            Route::get('/admin/schools', [App\Http\Controllers\AdminController::class, 'manageSchools'])->name('manage.schools');
            Route::post('/api/dte/dt-schools', [App\Http\Controllers\DteController::class, 'manageSchools'])->name('dte.manage.schools');

            Route::get('/admin/district-offices', [App\Http\Controllers\AdminController::class, 'manageDistrictOffices'])->name('manage.district-offices');
            Route::post('/api/dte/dt-district-offices', [App\Http\Controllers\DteController::class, 'manageDistrictOffices'])->name('dte.manage.district-offices');

            Route::get('/admin/learners/template', [App\Http\Controllers\LearnerAdminController::class, 'downloadTemplate'])->name('manage.learners.template');
            Route::post('/admin/learners/upload', [App\Http\Controllers\LearnerAdminController::class, 'bulkUpload'])->name('manage.learners.upload');

            // Academic year management
            Route::get('/admin/academic-years', [App\Http\Controllers\AdminController::class, 'manageAcademicYears'])->name('manage.academic-years');
            Route::get('/api/admin/academic-years', [App\Http\Controllers\AdminController::class, 'listAcademicYears'])->name('admin.academic-years.list');
            Route::post('/api/admin/academic-years/save', [App\Http\Controllers\AdminController::class, 'saveAcademicYear'])->name('admin.academic-years.save');
            Route::post('/api/admin/academic-years/activate', [App\Http\Controllers\AdminController::class, 'activateAcademicYear'])->name('admin.academic-years.activate');

           });
    });
});

Route::get('/not-authorised', function () {
    return view('auth.not-authorised');
})->name('not-authorised');

