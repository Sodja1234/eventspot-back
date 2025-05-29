<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Route utilisateur protégée
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user()->load('organisateur'),
            'email_verified' => $request->user()->hasVerifiedEmail()
        ]);
    });
    
    // Ajoutez ici vos autres routes protégées
});

Route::apiResource('evenements', EventController::class);
Route::apiResource('categories', CategoryController::class);
Route::get('/search', [EventController::class, 'index']);
require __DIR__.'/auth.php';