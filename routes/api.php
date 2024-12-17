<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\StudioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:api');

Route::middleware(['auth:api'])->group(function () {
    // Endpoint khusus admin
    Route::post('/film/create', [FilmController::class, 'store']);
    Route::post('/film/update/{id}', [FilmController::class, 'update']);
    Route::delete('/film/delete/{id}', [FilmController::class, 'destroy']);
    Route::get('/user', [UserController::class, 'index']);
    Route::post('/user/update/{id}', [UserController::class, 'update']);
    Route::delete('/user/delete/{id}', [UserController::class, 'destroy']);

    // Endpoint untuk studio
    Route::prefix('/film/{filmId}')->group(function () {
        Route::get('/studios', [StudioController::class, 'showStudiosByFilm']);
        Route::post('/studios', [StudioController::class, 'store']);
    });

    Route::resource('studios', StudioController::class)->except(['create', 'edit']);

    // Endpoint user tanpa middleware role
    Route::get('/film', [FilmController::class, 'index']);
    Route::post('/film/search', [FilmController::class, 'search']);

    // Endpoint profil user
    Route::get('/profile', [UserController::class, 'profile']);
});

