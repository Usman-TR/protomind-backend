<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KeywordController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\ProtocolController;
use App\Http\Controllers\Api\ProtocolDocumentController;
use App\Http\Controllers\Api\ProtocolMemberController;
use App\Http\Controllers\Api\ProtocolTaskController;
use App\Http\Controllers\Api\ChangePasswordController;
use App\Http\Controllers\Api\StatController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('change-password')->controller(ChangePasswordController::class)->group(function () {
    Route::post('reset', 'reset');
    Route::post('check-token', 'checkToken');
    Route::post('send-link', 'sendResetLinkEmail');
});


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');

    Route::group(["middleware" => "auth:api"], function() {
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get("me", "me");
    });
});

Route::group(["middleware" => "auth:api"], function() {
    Route::apiResource('users', UserController::class)->except('destroy');

    Route::get('protocols/tasks', [ProtocolTaskController::class, 'index']);
    Route::post('protocols/{id}/tasks', [ProtocolTaskController::class, 'store']);
    Route::put('protocols/tasks/{id}', [ProtocolTaskController::class, 'update']);
    Route::delete('protocols/tasks/{id}', [ProtocolTaskController::class, 'destroy']);

    Route::post('protocols/{id}/final', [ProtocolController::class, 'saveFinalTranscript']);
    Route::get('protocols/{id}/final', [ProtocolController::class, 'getFinalTranscript']);
    Route::post('protocols/{id}/process-video', [ProtocolController::class, 'runVideoProcessing']);
    Route::apiResource('protocols', ProtocolController::class);
    Route::apiResource('protocols/{id}/members', ProtocolMemberController::class)->except('destroy', 'update');
    Route::delete('protocols/members/{id}', [ProtocolMemberController::class, 'destroy']);

    Route::get('protocols/{id}/documents/docx', [ProtocolDocumentController::class, 'generateDocx']);
    Route::get('protocols/{id}/documents/pdf', [ProtocolDocumentController::class, 'generatePdf']);


    Route::prefix('keywords')->controller(KeywordController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::apiResource('meetings', MeetingController::class);


    Route::get('stats/manager/all', [StatController::class, 'getManagerEntitiesStat']);

    Route::get('stats/secretary/meetings', [StatController::class, 'getSecretaryMeetingsStat']);
    Route::get('stats/secretary/{id}/entities', [StatController::class, 'getSecretaryEntitiesStat']);
    Route::get('stats/secretary/{id}/tasks', [StatController::class, 'getSecretaryTasksStat']);
});
