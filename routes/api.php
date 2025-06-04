<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PalabraController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\DiccionarioController;
use App\Http\Controllers\EtiquetaController;
use App\Http\Controllers\AmigoController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DailyChallengeController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SuggestionController;


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
Route::get('/getUserDataByName/{username}/{userID}', [UserController::class, 'getUserDataByName']);
Route::get('/users', [UserController::class, 'index']);
Route::patch('banUser', [UserController::class, 'banUser']);

// Funciones referentes a Palabra
Route::post('/registerWord', [PalabraController::class, 'store']);
Route::get('/getWords/{letter}', [PalabraController::class, 'getWords']);
Route::get('/getRequiredWords', [PalabraController::class, 'getRequiredWords']);
Route::get('/getRandomWords', [PalabraController::class, 'getRandomWords']);
Route::get('/getVideosByWord/{word}', [PalabraController::class, 'getVideosByWord']);


// Funciones referentes a Video
Route::get('getVideos/{userID}/{descripcion}',[VideoController::class, 'getVideos'])->where('descripcion', '.*');
Route::post('storeVideo', [VideoController::class, 'store']);
Route::post('/videoLikes', [VideoController::class, 'videoLikes']);
Route::post('/reportVideo', [VideoController::class, 'reportVideo']);
Route::post('/cancelMyAction', [VideoController::class, 'cancelMyAction']);
Route::get('/getRecentlyUploadedVideos/{userID}', [VideoController::class, 'getRecentlyUploadedVideos']);
Route::get('/getVideosByThemes/{userID}/{tags}', [VideoController::class, 'getVideosByThemes']);
Route::get('/getVideosUncorrected/{userID}', [VideoController::class, 'getVideosUncorrected']);
Route::get('/getVideosCorrected/{userID}', [VideoController::class, 'getVideosCorrected']);
Route::get('/getUnseenVideosCorrected/{userID}', [VideoController::class, 'getUnseenVideosCorrected']);
Route::post('/correctVideo', [VideoController::class, 'correctVideo']);
Route::get('/getMyFriendsVideos/{userID}', [VideoController::class, 'getMyFriendsVideos']);
Route::get('/getExpertStatData', [VideoController::class, 'getExpertStatData']);
Route::patch('/banVideo', [VideoController::class, 'banVideo']);


// Funciones referentes a Diccionario
Route::post('/storeVideoInDictionary', [DiccionarioController::class, 'storeVideoInDictionary']);
Route::post('/deleteVideoFromDictionary', [DiccionarioController::class, 'deleteVideoFromDictionary']);
Route::get('/getPersonalDictionary/{userID}', [DiccionarioController::class, 'getPersonalDictionary']);
Route::get('/testYourself/{userID}', [DiccionarioController::class, 'testYourself']);
Route::get('/getDailyChallenge', [DiccionarioController::class, 'getDailyChallenge']);


// Funciones referentes a Etiquetas 
Route::get('/getTags', [EtiquetaController::class, 'get']);

// Funciones referentes a Amigos
Route::get('/getPendingFriendRequest/{to}', [AmigoController::class, 'getPendingFriendRequest']);
Route::post('/sendFriendRequest', [AmigoController::class, 'sendFriendRequest']);
Route::get('/amIBeingAddedByOwner/{from}/{to}', [AmigoController::class, 'amIBeingAddedByOwner']);
Route::get('/isMyFriend/{from}/{to}', [AmigoController::class, 'isMyFriend']);
Route::post('/acceptFriend', [AmigoController::class, 'acceptFriend']);
Route::post('/denyRequest', [AmigoController::class, 'denyRequest']);
Route::get('/getNotFriendsUsers/{userID}', [AmigoController::class, 'getNotFriendsUsers']);
Route::get('/getFriends/{userID}', [AmigoController::class, 'getFriends']);


// Funciones referentes a Chat
Route::get('/getMyConversations/{userID}', [ChatController::class, 'getMyConversations']);
Route::post('/sendMessage', [ChatController::class, 'sendMessage']);
Route::patch('/markChatAsRead', [ChatController::class, 'markChatAsRead']);

// Funciones referentes a Sugerencias
Route::post('/sendSuggestion', [SuggestionController::class, 'sendSuggestion']);

// Funciones referentes a DailyChallenge
Route::get('/checkLastDailyChallenge/{userID}', [DailyChallengeController::class, 'checkLastDailyChallenge']);
Route::post('/sendResults', [DailyChallengeController::class, 'sendResults']);

// Funciones referentes a Reportes
Route::get('/getAllReports', [ReporteController::class, 'getAllReports']);
Route::patch('/hideReport', [ReporteController::class, 'hideReport']);


