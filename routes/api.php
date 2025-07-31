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
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user()->load('organisateur'),
            'email_verified' => $request->user()->hasVerifiedEmail()
        ]);
    });

    Route::prefix('events')->group(
        function () {
            Route::get('auth/{id}', [EventController::class, 'show']);
            Route::get('auth', [EventController::class, 'index']);
            Route::post('{event_id}/favorite', [FavoriteController::class, '__invoke']);
            Route::get('{event_id}/favorite', [FavoriteController::class, 'show']);
            Route::post('{event_id}/subscribe', [SubscribeController::class, '__invoke']);
            Route::get('{event_id}/subscribe', [SubscribeController::class, 'show']);
            Route::get('subsscribe', [SubscribeController::class, 'listSouscriptions']);
            Route::get('/subscribe/{id}', [EventController::class, 'show']);
            Route::get('/favorites', [EventController::class, 'getUserFavorites']);
            Route::get('/user/events', [EventController::class, 'getUserEvents']);
        }
    );

    Route::post('/user/edit/{id}', [UserController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'organisateur'])->group(function () {
    Route::prefix('events')->group(
        function () {
            Route::post('', [EventController::class, 'store']);
            Route::get('{event_id}/subscribers', [SubscribeController::class, 'getSubscribers']);
        }
    );
});
Route::prefix('categories')->group(
    function () {
        Route::get('{id}', [CategoryController::class, 'show']);
        Route::post('', [CategoryController::class, 'store']);
        Route::get('', [CategoryController::class, 'index']);
    }
);

Route::prefix('/search')->group(
    function () {
        Route::get('', [EventController::class, 'index']);
        Route::get('category/{name}', [CategoryController::class, 'index']);
        Route::get('user/{name}', [UserController::class, 'index']);
    }
);
Route::prefix('events')->group(
    function () {
        Route::get('/{id}', [EventController::class, 'show']);
        Route::get('', [EventController::class, 'index']);
    }
);

Route::get('user/{id}', [UserController::class, 'show']);
require __DIR__.'/auth.php';
