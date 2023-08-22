<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\SaleItemController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\ProductController;

use App\Http\Middleware\OnlyAllowJson;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware([OnlyAllowJson::class])->group(function() {
//     Route::apiResource('/sale', SaleController::class);
//     Route::apiResource('/product', ProductController::class);
//     Route::apiResource('/customer', CustomerController::class);
// });

Route::apiResource('/customer', CustomerController::class);
Route::apiResource('/product', ProductController::class);
Route::apiResource('/purchase', PurchaseController::class);
Route::apiResource('/purchase.item', PurchaseItemController::class);
Route::apiResource('/sale', SaleController::class);
Route::apiResource('/sale.item', SaleItemController::class);
Route::apiResource('/supplier', SupplierController::class);
