<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PalabraController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\DiccionarioController;
use App\Http\Controllers\EtiquetaController;


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
Route::get('/getUserData/{ownerID}/{userID}', [UserController::class, 'getUserData']);
Route::patch('updateProfile/{user}', [UserController::class, 'update']);

// Funciones referentes a Palabra
Route::post('/registerWord', [PalabraController::class, 'store']);
Route::get('/getWords/{letter}', [PalabraController::class, 'getWords']);
Route::get('/getWords/{letter}', [PalabraController::class, 'getWords']);
Route::get('/getRequiredWords', [PalabraController::class, 'getRequiredWords']);
Route::get('/getRandomWords', [PalabraController::class, 'getRandomWords']);


// Funciones referentes a Video
Route::get('/getVideos/{descripcion}/{userID}', [VideoController::class, 'getVideos']);
Route::post('storeVideo', [VideoController::class, 'store']);
Route::post('/videoLikes', [VideoController::class, 'videoLikes']);
Route::post('/reportVideo', [VideoController::class, 'reportVideo']);
Route::post('/cancelMyAction', [VideoController::class, 'cancelMyAction']);
Route::get('/getRecentlyUploadedVideos/{userID}', [VideoController::class, 'getRecentlyUploadedVideos']);
Route::get('/getVideosByThemes/{userID}/{tags}', [VideoController::class, 'getVideosByThemes']);
Route::get('/getVideosUncorrected', [VideoController::class, 'getVideosUncorrected']);
Route::get('/getVideosCorrected/{userID}', [VideoController::class, 'getVideosCorrected']);
Route::post('/correctVideo', [VideoController::class, 'correctVideo']);



// Funciones referentes a Diccionario
Route::post('/storeVideoInDictionary', [DiccionarioController::class, 'storeVideoInDictionary']);
Route::post('/deleteVideoFromDictionary', [DiccionarioController::class, 'deleteVideoFromDictionary']);
Route::get('/getPersonalDictionary/{userID}', [DiccionarioController::class, 'getPersonalDictionary']);
Route::get('/testYourself/{userID}', [DiccionarioController::class, 'testYourself']);

// Funciones referentes a Etiquetas 
Route::get('/getTags', [EtiquetaController::class, 'get']);



