<?php

use App\Http\Controllers\Api\BranchController;
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
    Route::get('/all_users', [UserController::class, 'getAll']);
    Route::post('/create_users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/edit_users/{id}', [UserController::class, 'update']);
    Route::put('/delete_users/{id}', [UserController::class, 'destroy']);
    Route::put('/reset_password/{id}', [UserController::class, 'resetPassword']);

    // Branch
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/all_branches', [BranchController::class, 'getAll']);
    Route::post('/create_branches', [BranchController::class, 'store']);
    Route::get('/branches/{id}', [BranchController::class, 'show']);
    Route::put('/edit_branches/{id}', [BranchController::class, 'update']);
    Route::put('/delete_branches/{id}', [BranchController::class, 'destroy']);
});


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    //
});


Route::middleware(['auth:sanctum', 'role:kurir'])->group(function () {
    //
});
