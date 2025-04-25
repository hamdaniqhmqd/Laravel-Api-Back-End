<?php

use App\Http\Controllers\Api\InvoiceRentalExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test_invoice', [InvoiceRentalExportController::class, 'testInvoiceView']);
