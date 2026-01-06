<?php

use App\Http\Controllers\Api\ChannelController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\StatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes publiques (pour login/register via Breeze)

// Routes protégées par authentification Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user()->load('status');
    });

    // Statuts
    Route::prefix('statuses')->group(function () {
        Route::get('/', [StatusController::class, 'index']);
        Route::post('/user', [StatusController::class, 'updateUserStatus']);
        Route::post('/heartbeat', [StatusController::class, 'heartbeat']);
        Route::get('/online-users', [StatusController::class, 'onlineUsers']);
    });

    // Channels (conversations et groupes)
    Route::prefix('channels')->group(function () {
        Route::get('/', [ChannelController::class, 'index']);
        Route::post('/', [ChannelController::class, 'store']);
        Route::get('/{id}', [ChannelController::class, 'show']);
        Route::put('/{id}', [ChannelController::class, 'update']);
        Route::delete('/{id}', [ChannelController::class, 'destroy']);
        Route::post('/{id}/members', [ChannelController::class, 'addMember']);
        Route::delete('/{id}/members', [ChannelController::class, 'removeMember']);
        Route::post('/{id}/leave', [ChannelController::class, 'leave']);
    });

    // Messages
    Route::prefix('channels/{channelId}/messages')->group(function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::post('/', [MessageController::class, 'store']);
        Route::get('/{id}', [MessageController::class, 'show']);
        Route::put('/{id}', [MessageController::class, 'update']);
        Route::delete('/{id}', [MessageController::class, 'destroy']);
        Route::post('/mark-as-read', [MessageController::class, 'markAsRead']);
    });

    // Messages non lus
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);
});
