<?php

use App\Http\Controllers\GenreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\API\MovieController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CastController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CastMovieController;
use App\Http\Controllers\ProfileController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * route resource cast
 */

Route::prefix("/v1")->group(function(){

    Route::apiResource('/cast', CastController::class);
    Route::apiResource('/genre', GenreController::class);
    Route::apiResource('/movie', MovieController::class);
    Route::apiResource('/cast-movie', CastMovieController::class);

    Route::prefix("/auth")->group(function(){
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });
    
    Route::middleware('auth:api')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post("/auth/generate-otp-code", [AuthController::class, 'generateOtpCode']);
        Route::post("/auth/verifikasi", [AuthController::class, 'verifikasi']);
    });

    Route::middleware(['auth:api','isVerif'])->group(function () {
        Route::post('/update-user', [AuthController::class, 'update']);
        Route::apiResource('/profile', ProfileController::class);
        Route::apiResource('/review', ReviewController::class);
    });

    Route::middleware(['auth:api','isAdmin'])->group(function () {
        Route::apiResource('/role', RolesController::class);
    });
});

