<?php

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TransactionsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');

// Product
Route::post('/products', [ProductsController::class, 'save'])->middleware('auth:sanctum');
Route::put('/products/{id}', [ProductsController::class, 'updateProduct'])->middleware('auth:sanctum');
Route::delete('/products/{id}', [ProductsController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/products', [ProductsController::class, 'index'])->middleware('auth:sanctum');
Route::get('/products/{id}', [ProductsController::class, 'getProductById'])->middleware('auth:sanctum');

// Transaction
Route::post('/transaction', [TransactionsController::class, 'createTransaction'])->middleware('auth:sanctum');
Route::get('/transaction', [TransactionsController::class, 'GetListTransaction'])->middleware('auth:sanctum');
Route::get('/transaction/{id}', [TransactionsController::class, 'GetListTransactionById'])->middleware('auth:sanctum');


// Route::get('/user', function () {
//     echo "Register";
// });