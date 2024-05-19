<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'events'], function () {
    Route::get('/', [EventController::class, 'getAllEvents']);
    Route::get('/{event}', [EventController::class, 'getEvent']);
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('orders', [OrderController::class, 'createOrder']);
Route::post('update_order_status', [OrderController::class, 'updateOrderStatus']);

Route::middleware(['auth'])->group(function () {
   Route::get('my_orders', [OrderController::class, 'getUserOrders']);
   Route::put('orders/{order}/refund', [OrderController::class, 'refund']);
});

Route::middleware(['auth', 'role:MANAGER'])->group(function () {
    Route::group(['prefix' => 'events'], function () {
        Route::post('/', [EventController::class, 'create']);
        Route::put('/{event}', [EventController::class, 'update']);
    });
});
