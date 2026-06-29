<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ref\CompanyController;
use App\Http\Controllers\Ref\CategoryController;
use App\Http\Controllers\Ref\MenuController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CustomerController;



// --- PUBLIC (Customer Flow) ---
Route::prefix('v1/customer')->group(function () {
    Route::get('/scan/{outlet_id}/{table_no}', [CustomerController::class, 'verifyTable']);
    Route::get('/menus', [CustomerController::class, 'getMenus']);
    Route::post('/checkout', [CustomerController::class, 'checkout']);
});

// --- AUTHENTICATION ---
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Route Terproteksi (Wajib membawa Bearer Token Sanctum)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // API Khusus Management (Admin/Owner)
    Route::prefix('ref')->group(function () {
        Route::apiResource('companies', CompanyController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('menus', MenuController::class);
    });
});