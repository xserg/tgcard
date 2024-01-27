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

Route::get('/handle', [TelegramController::class, 'handle']);

//Route::get('/' . env('TELEGRAM_BOT_TOKEN') . '/webhook', [TelegramController::class, 'handle']);
Route::post('/' . env('TELEGRAM_BOT_TOKEN') . '/webhook', [TelegramController::class, 'handle']);
