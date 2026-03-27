<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;


// Home → go to dashboard.
Route::redirect('/', '/dashboard');

// Main pages.
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Customer Related Routes
Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

// Deposit Related Routes
Route::post('/deposits', [DepositController::class, 'store'])->name('deposits.store');
Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');

// Account Related Routes
Route::get('/accounts', [AccountController::class, 'index'])->name('accounts');
Route::get('/accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');
