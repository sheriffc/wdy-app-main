<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => 'throttle:1000,1'], function () {
    //android admin
    Route::post('mobile/registration', [App\Http\Controllers\AndroidController::class, 'registration']);
    Route::any('trace', [App\Http\Controllers\AndroidController::class, 'remoteStackTrace']);
    Route::any('mobile/appversion', [App\Http\Controllers\AndroidController::class, 'checkLatestAppVersion']);
    //data sync
    Route::post('mobile/sync/checkin', [App\Http\Controllers\AndroidSync\ApiController::class, 'checkIn']);
    Route::post('mobile/sync/token', [App\Http\Controllers\AndroidSync\ApiController::class, 'checkAndUpdateToken']);
    Route::post('mobile/sync/download', [App\Http\Controllers\AndroidSync\ApiController::class, 'handleDownloadDataRequest']);
    Route::post('mobile/sync/upload', [App\Http\Controllers\AndroidSync\ApiController::class, 'handleUploadDataRequest']);
    Route::post('mobile/sync/upload-db-receipt', [App\Http\Controllers\AndroidSync\ApiController::class, 'uploadDbReceipt']);
    Route::post('mobile/sync/upload-file', [App\Http\Controllers\AndroidSync\ApiController::class, 'uploadFile']);
    Route::post('mobile/teachers/search', [App\Http\Controllers\AndroidSync\ApiController::class, 'searchTeachers']);
});

//public api password resets
Route::group(['middleware' => 'throttle:1000,10'], function () {
    Route::post('mobile/password-reset/status', [App\Http\Controllers\AndroidPasswordReset\ApiController::class, 'checkStatus']);
    Route::post('mobile/password-reset/request-change', [App\Http\Controllers\AndroidPasswordReset\ApiController::class, 'requestPasswordChange']);
    Route::post('mobile/password-reset/cancel', [App\Http\Controllers\AndroidPasswordReset\ApiController::class, 'cancelRequest']);
});

