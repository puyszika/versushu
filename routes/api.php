<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LobbyController;
use App\Http\Controllers\MatchmakingController;


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


Route::middleware('auth:sanctum')->group(function(){
    Route::post('/lobby', [LobbyController::class, 'store']);
    Route::post('/lobby/{code}/join', [LobbyController::class, 'join']);
    Route::post('/lobby/{code}/map-action', [LobbyController::class, 'mapAction']);
    Route::post('/lobby/{code}/join', [LobbyController::class, 'joinSlot']);
    Route::post('/lobby/{code}/spectate', [LobbyController::class, 'joinSpectator']);


    Route::post('/match-proposal/{id}/accept', [MatchProposalController::class, 'accept']);
    Route::post('/match-proposal/{id}/decline', [MatchProposalController::class, 'decline']);

    // routes/api.php
Route::post('/matchmaking/join', [MatchmakingController::class, 'join']);
Route::post('/matchmaking/leave', [MatchmakingController::class, 'leave']);


});

Route::get('/lobby/{code}/maps', function($code) {
    $lobby = \App\Models\Lobby::where('code', $code)->firstOrFail();
    return $lobby->maps()->get(['map_name', 'status']);
});


Route::middleware('auth:sanctum')->get('/debug-user', function (Request $request) {
    return $request->user();
});

Route::get('/debug-user', function () {
    return [
        'user' => auth()->user(),
        'session' => session()->all(),
        'guard' => Auth::getDefaultDriver(),
        'cookie' => request()->cookie('laravel_session'),
    ];
});
Route::get('/lobby/{code}/maps', [LobbyController::class, 'getMaps']);

