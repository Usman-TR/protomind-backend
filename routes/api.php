<?php

use App\Http\Controllers\Api\AuthController;
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
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{id}', [UserController::class, 'update']);
});
