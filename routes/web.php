<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\MetalTypeController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

// Home → go to dashboard.
Route::redirect('/', '/dashboard');

// Main pages.
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Customer Related Routes
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

// Deposit Related Routes
Route::post('/deposits', [DepositController::class, 'store'])->name('deposits.store');
Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');

// Account Related Routes
Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
Route::get('/accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');

// Metal Type Related Routes
Route::get('/metal-types', [MetalTypeController::class, 'index'])->name('metal-types.index');
Route::get('/metal-types/create', [MetalTypeController::class, 'create'])->name('metal-types.create');
Route::post('/metal-types', [MetalTypeController::class, 'store'])->name('metal-types.store');
Route::put('/metal-types/{metalType}', [MetalTypeController::class, 'update'])->name('metal-types.update');
Route::patch('/metal-types/{metalType}', [MetalTypeController::class, 'update']);
Route::delete('/metal-types/{metalType}', [MetalTypeController::class, 'destroy'])->name('metal-types.destroy');
Route::get('/metal-types/{metalType}', [MetalTypeController::class, 'show'])->name('metal-types.show');
