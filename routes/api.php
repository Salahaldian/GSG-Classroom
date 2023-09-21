<?php

use App\Http\Controllers\Api\V1\AccessTokenController;
use App\Http\Controllers\Api\V1\ClassroomController;
use App\Http\Controllers\Api\V1\ClassworkController;
use App\Http\Controllers\Api\V1\ClassroomMessagesController;
use Illuminate\Http\Request;
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

Route::prefix('v1')->group(function() {
    Route::middleware('auth:sanctum')->group(function() {
        Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
            return $request->user();
        });

        Route::get('auth/access-tokens', [AccessTokenController::class, 'index']);
        Route::delete('auth/access-tokens/{id?}', [AccessTokenController::class, 'destroy']);

        Route::apiResource('classrooms', ClassroomController::class);
        Route::apiResource('classrooms.classworks', ClassworkController::class);
        Route::apiResource('classrooms.messages', ClassroomMessagesController::class);
    });
    Route::middleware('guest:sanctum')->group(function() {
        Route::post('auth/access-tokens', [AccessTokenController::class, 'store']);
    });
});


