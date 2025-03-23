<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth
// Route::post('/register', RegisterController::class); untuk test register saja, jika mau digunakan, jangan lupa atur controller nya
Route::post('/login', LoginController::class)->name('login');
Route::post('/logout', LogoutController::class)->middleware('auth:sanctum')->name('logout');

Route::middleware(['auth:sanctum', 'role:owner'])->group(function () {
    // Users
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    //
});


Route::middleware(['auth:sanctum', 'role:kurir'])->group(function () {
    //
});
