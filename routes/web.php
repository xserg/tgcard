<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/telegram', [TelegramController::class, 'test']);
Route::get('/handle', [TelegramController::class, 'handle']);
Route::get('/show', [TelegramController::class, 'show']);

//Route::get('/' . env('TELEGRAM_BOT_TOKEN') . '/webhook', [TelegramController::class, 'handle']);
Route::post('/' . env('TELEGRAM_BOT_TOKEN') . '/webhook', [TelegramController::class, 'handle']);

Route::get('/set-webhook/{url}', [TelegramController::class, 'setwebhook']);
