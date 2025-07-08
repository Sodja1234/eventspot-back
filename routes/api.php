<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\SubcribeCntroller;
use App\Http\Controllers\SubscribeCntroller;
use App\Http\Controllers\SubscribeController;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Route utilisateur protégée
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user()->load('organisateur'),
            'email_verified' => $request->user()->hasVerifiedEmail()
        ]);
    });

    Route::prefix('events')->group(
        function () {
            Route::post('{event_id}/favorite', [FavoriteController::class, '__invoke']);
            Route::get('{event_id}/favorite', [FavoriteController::class, 'show']);
            Route::post('{event_id}/subscribe', [SubscribeController::class, '__invoke']);
            Route::get('{event_id}/subscribe', [SubscribeController::class, 'show']);
            Route::get('subsscribe', [SubscribeController::class, 'listSouscriptions']);
            Route::get('/subscribe/{id}', [EventController::class, 'show']);
        }
    );
    Route::get('/favorites', [EventController::class, 'getUserFavorites']);
});

Route::middleware(['auth:sanctum', 'organisateur'])->group(function () {
    // Routes réservées aux organisateurs
    Route::apiResource('evenements', EventController::class)->except(['index', 'show']);
    Route::get('/events/{event_id}/subscribers', [SubscribeController::class, 'getSubscribers']);
    Route::get('/event/last-event', [EventController::class, 'getLastEvent']);
    // ... autres routes pour organisateurs ...
});
Route::apiResource('evenements', EventController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class);
Route::prefix('/search')->group(
    function () {
        Route::get('', [EventController::class, 'index']);
        Route::get('category/{name}', [CategoryController::class, 'index']);
        Route::get('user/{name}', [UserController::class, 'index']);
    }
);
Route::prefix('events')->group(
    function () {
        Route::get('/id/{id}', [EventController::class, 'show']);
        Route::get('', [EventController::class, 'index']);
    }
);
Route::get('category/{id}', [CategoryController::class, 'show']);
Route::get('user/{id}', [UserController::class, 'show']);
require __DIR__.'/auth.php';