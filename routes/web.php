<?php

use Illuminate\Support\Facades\Route;


// Home → go to dashboard.
Route::redirect('/', '/dashboard');

// Main pages.
Route::view('/dashboard', 'dashboard')->name('dashboard');

// Customer Related Routes
Route::view('/customers', 'customers.index')->name('customers');
Route::view('/customers/create', 'customers.create')->name('customers.create');
Route::view('/customers/{customer}', 'customers.show')->name('customers.show');

// Account Related Routes
Route::view('/accounts', 'accounts.index')->name('accounts');
Route::view('/accounts/{account}', 'accounts.show')->name('accounts.show');
