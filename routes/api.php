<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ProtocolController;
use App\Http\Controllers\ProtocolKeywordController;
use App\Http\Controllers\ProtocolMemberController;
use App\Http\Controllers\ProtocolTaskController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('change-password/send-link', 'sendLinkChangePassword');
    Route::post('change-password/change', 'changePassword');

    Route::group(["middleware" => "auth:api"], function() {
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get("me", "me");
    });
});

Route::group(["middleware" => "auth:api"], function() {
    Route::apiResource('users', UserController::class)->except('destroy');

    Route::apiResource('protocols', ProtocolController::class)->except('destroy', 'update');
    Route::apiResource('protocols/{id}/members', ProtocolMemberController::class)->except('destroy', 'update');
    Route::delete('protocols/members/{id}', [ProtocolMemberController::class, 'destroy']);

    Route::post('protocols/{id}/tasks', [ProtocolTaskController::class, 'store']);
    Route::put('protocols/tasks/{id}', [ProtocolTaskController::class, 'update']);

    Route::get('protocols/{id}/keywords', [ProtocolKeywordController::class, 'index']);
    Route::put('protocols/{id}/keywords', [ProtocolKeywordController::class, 'update']);
    Route::post('protocols/{id}/keywords', [ProtocolKeywordController::class, 'store']);
    Route::delete('protocols/keywords/{id}', [ProtocolKeywordController::class, 'destroy']);
});
