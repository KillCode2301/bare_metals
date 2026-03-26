<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;


// Home → go to dashboard.
Route::redirect('/', '/dashboard');

// Main pages.
Route::view('/dashboard', 'dashboard')->name('dashboard');

// Customer Related Routes
Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

// Account Related Routes
Route::view('/accounts', 'accounts.index')->name('accounts');
Route::view('/accounts/{account}', 'accounts.show')->name('accounts.show');
