<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;


Route::post('/token/add', [AuthController::class, 'signin']);
Route::delete('/token/delete', [AuthController::class, 'delete_token']);
//Route::post('register', [AuthController::class, 'signup']);

Route::post('set-webhook', [AuthController::class, 'setwebhook'])->middleware('auth:sanctum');
Route::resource('orders', OrderController::class)->middleware('auth:sanctum');
Route::post('order-card', [OrderController::class, 'card'])->middleware('auth:sanctum');
