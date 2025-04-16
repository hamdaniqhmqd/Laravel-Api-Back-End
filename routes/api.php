<?php

use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\LaundryItemController;
use App\Http\Controllers\Api\ListTransactionLaundryController;
use App\Http\Controllers\Api\ListTransactionRentalController;
use App\Http\Controllers\Api\RentalItemController;
use App\Http\Controllers\Api\TransactionLaundryController;
use App\Http\Controllers\Api\TransactionRentalController;
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
    Route::get('/all_users_trashed', [UserController::class, 'getAllTrashed']);
    Route::post('/create_users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/edit_users/{id}', [UserController::class, 'update']);
    Route::delete('/delete_users/{id}', [UserController::class, 'destroy']);
    Route::put('/restore_users/{id}', [UserController::class, 'restore']);
    Route::delete('/force_destroy_users/{id}', [UserController::class, 'forceDestroy']);
    Route::put('/reset_password/{id}', [UserController::class, 'resetPassword']);

    // Branch
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/all_branches', [BranchController::class, 'getAll']);
    Route::get('/all_branches_trashed', [BranchController::class, 'getAllTrashed']);
    Route::post('/create_branches', [BranchController::class, 'store']);
    Route::get('/branches/{id}', [BranchController::class, 'show']);
    Route::put('/edit_branches/{id}', [BranchController::class, 'update']);
    Route::delete('/delete_branches/{id}', [BranchController::class, 'destroy']);
    Route::put('/restore_branches/{id}', [BranchController::class, 'restore']);
    Route::delete('/force_destroy_branches/{id}', [BranchController::class, 'forceDestroy']);

    // Client
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/all_clients', [ClientController::class, 'getAll']);
    Route::get('/all_clients_trashed', [ClientController::class, 'getAllTrashed']);
    Route::post('/create_clients', [ClientController::class, 'store']);
    Route::get('/clients/{id}', [ClientController::class, 'show']);
    Route::put('/edit_clients/{id}', [ClientController::class, 'update']);
    Route::delete('/delete_clients/{id}', [ClientController::class, 'destroy']);
    Route::put('/restore_clients/{id}', [ClientController::class, 'restore']);
    Route::delete('/force_destroy_clients/{id}', [ClientController::class, 'forceDestroy']);
    Route::get('/all_clients_with_branch', [ClientController::class, 'getBrachWithClient']);

    // Rental Items
    Route::get('/rental_items', [RentalItemController::class, 'index']);
    Route::get('/all_rental_items', [RentalItemController::class, 'getAll']);
    Route::get('/all_rental_items_trashed', [RentalItemController::class, 'getAllTrashed']);
    Route::post('/create_rental_items', [RentalItemController::class, 'store']);
    Route::get('/rental_items/{id}', [RentalItemController::class, 'show']);
    Route::put('/edit_rental_items/{id}', [RentalItemController::class, 'update']);
    Route::delete('/delete_rental_items/{id}', [RentalItemController::class, 'destroy']);
    Route::put('/restore_rental_items/{id}', [RentalItemController::class, 'restore']);
    Route::delete('/force_destroy_rental_items/{id}', [RentalItemController::class, 'forceDestroy']);

    // Laundry Items
    Route::get('/laundry_items', [LaundryItemController::class, 'index']);
    Route::get('/all_laundry_items', [LaundryItemController::class, 'getAll']);
    Route::get('/all_laundry_items_trashed', [LaundryItemController::class, 'getAllTrashed']);
    Route::post('/create_laundry_items', [LaundryItemController::class, 'store']);
    Route::get('/laundry_items/{id}', [LaundryItemController::class, 'show']);
    Route::put('/edit_laundry_items/{id}', [LaundryItemController::class, 'update']);
    Route::delete('/delete_laundry_items/{id}', [LaundryItemController::class, 'destroy']);
    Route::put('/restore_laundry_items/{id}', [LaundryItemController::class, 'restore']);
    Route::delete('/force_destroy_laundry_items/{id}', [LaundryItemController::class, 'forceDestroy']);

    // Transaction Laundry
    Route::get('/transaction_laundries', [TransactionLaundryController::class, 'index']);
    Route::get('/all_transaction_laundries', [TransactionLaundryController::class, 'getAll']);
    Route::get('/all_transaction_laundries_trashed', [TransactionLaundryController::class, 'getAllTrashed']);
    Route::post('/create_transaction_laundries', [TransactionLaundryController::class, 'store']);
    Route::get('/transaction_laundries/{id}', [TransactionLaundryController::class, 'show']);
    Route::put('/edit_transaction_laundries/{id}', [TransactionLaundryController::class, 'update']);
    Route::delete('/delete_transaction_laundries/{id}', [TransactionLaundryController::class, 'destroy']);
    Route::put('/restore_transaction_laundries/{id}', [TransactionLaundryController::class, 'restore']);
    Route::delete('/force_destroy_transaction_laundries/{id}', [TransactionLaundryController::class, 'forceDestroy']);
    Route::get('/transaction_laundries/{id}/list', [TransactionLaundryController::class, 'getListWithTransactionLaundry']);
    Route::post('/create_transaction_list_laundries', [TransactionLaundryController::class, 'storeListTransactionLaundry']);

    // List Transaction Laundry
    Route::get('/list_transaction_laundries', [ListTransactionLaundryController::class, 'index']);
    Route::get('/all_list_transaction_laundries', [ListTransactionLaundryController::class, 'getAll']);
    Route::get('/all_list_transaction_laundries_trashed', [ListTransactionLaundryController::class, 'getAllTrashed']);
    Route::post('/create_list_transaction_laundries', [ListTransactionLaundryController::class, 'store']);
    Route::get('/list_transaction_laundries/{id}', [ListTransactionLaundryController::class, 'show']);
    Route::put('/edit_list_transaction_laundries/{id}', [ListTransactionLaundryController::class, 'update']);
    Route::delete('/delete_list_transaction_laundries/{id}', [ListTransactionLaundryController::class, 'destroy']);
    Route::put('/restore_list_transaction_laundries/{id}', [ListTransactionLaundryController::class, 'restore']);
    Route::delete('/force_destroy_list_transaction_laundries/{id}', [ListTransactionLaundryController::class, 'forceDestroy']);

    // Transaction Rental
    Route::get('/transaction_rentals', [TransactionRentalController::class, 'index']);
    Route::get('/all_transaction_rentals', [TransactionRentalController::class, 'getAll']);
    Route::get('/all_transaction_rentals_trashed', [TransactionRentalController::class, 'getAllTrashed']);
    Route::post('/create_transaction_rentals', [TransactionRentalController::class, 'store']);
    Route::get('/transaction_rentals/{id}', [TransactionRentalController::class, 'show']);
    Route::put('/edit_transaction_rentals/{id}', [TransactionRentalController::class, 'update']);
    Route::delete('/delete_transaction_rentals/{id}', [TransactionRentalController::class, 'destroy']);
    Route::put('/restore_transaction_rentals/{id}', [TransactionRentalController::class, 'restore']);
    Route::delete('/force_destroy_transaction_rentals/{id}', [TransactionRentalController::class, 'forceDestroy']);
    Route::get('/transaction_rentals/{id}/list', [TransactionRentalController::class, 'getListWithTransactionRental']);
    Route::post('/create_transaction_list_rentals', [TransactionRentalController::class, 'storeListTransactionRental']);

    // List Transaction Rental
    Route::get('/list_transaction_rentals', [ListTransactionRentalController::class, 'index']);
    Route::get('/all_list_transaction_rentals', [ListTransactionRentalController::class, 'getAll']);
    Route::get('/all_list_transaction_rentals_trashed', [ListTransactionRentalController::class, 'getAllTrashed']);
    Route::post('/create_list_transaction_rentals', [ListTransactionRentalController::class, 'store']);
    Route::get('/list_transaction_rentals/{id}', [ListTransactionRentalController::class, 'show']);
    Route::put('/edit_list_transaction_rentals/{id}', [ListTransactionRentalController::class, 'update']);
    Route::delete('/delete_list_transaction_rentals/{id}', [ListTransactionRentalController::class, 'destroy']);
    Route::put('/restore_list_transaction_rentals/{id}', [ListTransactionRentalController::class, 'restore']);
    Route::delete('/force_destroy_list_transaction_rentals/{id}', [ListTransactionRentalController::class, 'forceDestroy']);
});


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    //
});


Route::middleware(['auth:sanctum', 'role:kurir'])->group(function () {
    //
});
