<?php

use App\Http\Controllers\BouquetController;
use App\Http\Controllers\FlowerController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/
Route::apiResource('flowers', FlowerController::class);
Route::apiResource('bouquets', BouquetController::class);
Route::apiResource('orders', OrderController::class);


