<?php

use Illuminate\Support\Facades\Route;

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

Route::apiResource('/product'       , ProductController::class);
Route::apiResource('/customer'      , CustomerController::class);
Route::apiResource('/sale'          , SaleController::class);
Route::apiResource('/sale.item'     , SaleItemController::class);
Route::apiResource('/supplier'      , SupplierController::class);
Route::apiResource('/purchase'      , PurchaseController::class);
Route::apiResource('/purchase.item' , PurchaseItemController::class);

Route::group(['prefix' => 'base'], function() {
    Route::apiResource('/product'       , \App\Http\Controllers\Base\ProductController::class);
    Route::apiResource('/customer'      , \App\Http\Controllers\Base\CustomerController::class);
    Route::apiResource('/sale'          , \App\Http\Controllers\Base\SaleController::class);
    Route::apiResource('/sale.item'     , \App\Http\Controllers\Base\SaleItemController::class);
    Route::apiResource('/supplier'      , \App\Http\Controllers\Base\SupplierController::class);
    Route::apiResource('/purchase'      , \App\Http\Controllers\Base\PurchaseController::class);
    Route::apiResource('/purchase.item' , \App\Http\Controllers\Base\PurchaseItemController::class);    
});
