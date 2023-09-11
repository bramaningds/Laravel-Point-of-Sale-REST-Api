<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\SaleItemController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\PurchaseItemController;

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

Route::apiResource('/category'      , CategoryController::class);
Route::apiResource('/product'       , ProductController::class);
Route::apiResource('/customer'      , CustomerController::class);
Route::apiResource('/sale'          , SaleController::class);
Route::apiResource('/sale.item'     , SaleItemController::class);
Route::apiResource('/supplier'      , SupplierController::class);
Route::apiResource('/purchase'      , PurchaseController::class);
Route::apiResource('/purchase.item' , PurchaseItemController::class);