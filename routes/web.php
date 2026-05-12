<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\AdminHome;
use App\Http\Controllers\Admin\AdminHouse;
use App\Http\Controllers\Admin\AdminResident;
use App\Http\Controllers\Admin\AdminOffice;
use App\Http\Controllers\Admin\AdminFeeType;
use App\Http\Controllers\Admin\AdminExpenseCategory;
use App\Http\Controllers\Admin\AdminPaymentBill;
use App\Http\Controllers\Admin\AdminExpense;
use App\Http\Controllers\Admin\AdminFinanceReport;

Route::get('/', function () {
    return redirect('/admin');
});

// ADMIN AUTH
Route::get('/login', [AdminAuthController::class, 'index'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);
Route::get('/logout', [AdminAuthController::class, 'logout']);

// ADMIN PAGE
Route::group(['prefix'=> 'admin','middleware'=>['auth:admin']], function(){
    Route::get('/', [AdminHome::class, 'index']);
    Route::get('/home', [AdminHome::class, 'index']);
    
    Route::get('/house', [AdminHouse::class, 'index']);
    Route::post('/house', [AdminHouse::class, 'postHandler']);

    Route::get('/house/{house}/resident', [AdminResident::class, 'index']);
    Route::post('/house/{house}/resident', [AdminResident::class, 'postHandler']);

    // Fee Types (Jenis Iuran)
    Route::get('/fee-type', [AdminFeeType::class, 'index']);
    Route::post('/fee-type', [AdminFeeType::class, 'postHandler']);

    // Expense Categories (Kategori Pengeluaran)
    Route::get('/expense-category', [AdminExpenseCategory::class, 'index']);
    Route::post('/expense-category', [AdminExpenseCategory::class, 'postHandler']);

    // Payment Bills (Tagihan Bulanan)
    Route::get('/payment-bill', [AdminPaymentBill::class, 'index']);
    Route::post('/payment-bill', [AdminPaymentBill::class, 'postHandler']);
    Route::post('/payment-bill/generate', [AdminPaymentBill::class, 'generate']);
    Route::post('/payment-bill/pay', [AdminPaymentBill::class, 'pay']);
    Route::post('/payment-bill/pay-bulk', [AdminPaymentBill::class, 'payBulk']);

    // Expenses (Pengeluaran)
    Route::get('/expense', [AdminExpense::class, 'index']);
    Route::post('/expense', [AdminExpense::class, 'postHandler']);

    // Finance Report (Laporan Keuangan)
    Route::get('/finance-report', [AdminFinanceReport::class, 'index']);
});

// API
Route::group(['prefix'=> 'api'], function(){
    Route::get('user/{data:id}', [APIController::class, 'user']);
    Route::get('users', [APIController::class, 'users']);
    Route::get('house/{data:id}', [APIController::class, 'house']);
    Route::get('house/{houseId}/residents', [APIController::class, 'residentsByHouse']);
    Route::get('resident/{data:id}', [APIController::class, 'resident']);
    Route::get('fee-type/{data:id}', [APIController::class, 'feeType']);
    Route::get('expense-category/{data:id}', [APIController::class, 'expenseCategory']);
    Route::get('payment-bill/{data:id}', [APIController::class, 'paymentBill']);
    Route::get('expense/{data:id}', [APIController::class, 'expense']);
});