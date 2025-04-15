<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PalabraController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\DiccionarioController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Funciones referentes a User
Route::post('/register', [UserController::class, 'store']);
Route::get('/login/{email}/{password}', [UserController::class, 'getUser']);
Route::post('/verificar-otp', [UserController::class, 'verificarOtp']);
Route::get('/forgot-password/{email}', [UserController::class, 'forgotPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);
Route::get('/verificar-otp-password/{email}/{otp}', [UserController::class, 'verificarOtpPassword']);

// Funciones referentes a Palabra
Route::post('/registerWord', [PalabraController::class, 'store']);
Route::get('/getWords/{letter}', [PalabraController::class, 'getWords']);
Route::get('/getVideosByWord/{word}', [PalabraController::class, 'getVideosByWord']);
Route::get('/getRandomWords', [PalabraController::class, 'getRandomWords']);


// Funciones referentes a Video
Route::get('/getVideos/{descripcion}/{userID}', [VideoController::class, 'getVideos']);
Route::post('storeVideo', [VideoController::class, 'store']);
Route::post('/videoLikes', [VideoController::class, 'videoLikes']);
Route::post('/reportVideo', [VideoController::class, 'reportVideo']);
Route::post('/cancelMyAction', [VideoController::class, 'cancelMyAction']);

// Funciones referentes a Diccionario
Route::post('/storeVideoInDictionary', [DiccionarioController::class, 'storeVideoInDictionary']);
Route::post('/deleteVideoFromDictionary', [DiccionarioController::class, 'deleteVideoFromDictionary']);
Route::get('/getPersonalDictionary/{userID}', [DiccionarioController::class, 'getPersonalDictionary']);



