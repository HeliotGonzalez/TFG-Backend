<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PalabraController;
use App\Http\Controllers\VideoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'store']);
Route::get('/login/{email}/{password}', [UserController::class, 'getUser']);
Route::post('/verificar-otp', [UserController::class, 'verificarOtp']);
Route::get('/forgot-password/{email}', [UserController::class, 'forgotPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);
Route::get('/verificar-otp-password/{email}/{otp}', [UserController::class, 'verificarOtpPassword']);
Route::post('/registerWord', [PalabraController::class, 'store']);
Route::get('/getWords/{letter}', [PalabraController::class, 'getWords']);
Route::get('/getVideos/{descripcion}', [VideoController::class, 'getVideos']);
Route::post('/videoLikes', [VideoController::class, 'videoLikes']);

