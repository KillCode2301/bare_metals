<?php

use Illuminate\Support\Facades\Route;


// Home → go to dashboard.
Route::redirect('/', '/dashboard');

// Main pages.
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/customers', 'customers')->name('customers');
Route::view('/accounts', 'accounts')->name('accounts');
Route::view('/deposits', 'deposits')->name('deposits');
Route::view('/withdrawals', 'withdrawals')->name('withdrawals');
