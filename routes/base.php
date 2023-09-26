<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Base\CategoryController;
use App\Http\Controllers\Base\CustomerController;
use App\Http\Controllers\Base\ProductController;
use App\Http\Controllers\Base\SaleController;
use App\Http\Controllers\Base\SaleItemController;
use App\Http\Controllers\Base\SupplierController;
use App\Http\Controllers\Base\PurchaseController;
use App\Http\Controllers\Base\PurchaseItemController;

Route::apiResource('/category'      , CategoryController::class);
Route::apiResource('/product'       , ProductController::class);
Route::apiResource('/customer'      , CustomerController::class);
Route::apiResource('/sale'          , SaleController::class);
Route::apiResource('/sale.item'     , SaleItemController::class);
Route::apiResource('/supplier'      , SupplierController::class);
Route::apiResource('/purchase'      , PurchaseController::class);
Route::apiResource('/purchase.item' , PurchaseItemController::class);

Route::get('/product/{id}/purchase-prices', function($id) {

    $prices = \App\Models\Product::with('purchases')->findOrFail($id)->purchases->map(fn($purchase) => $purchase->pivot->price);

    return [
        'average' => $prices->average(),
        'max' => $prices->max(),
        'min' => $prices->min()
    ];
});

Route::get('/product/{id}/sale-prices', function($id) {

    $prices = \App\Models\Product::with('sales')->findOrFail($id)->sales->map(fn($sale) => $sale->pivot->price);

    return [
        'average' => $prices->average(),
        'max' => $prices->max(),
        'min' => $prices->min()
    ];
});