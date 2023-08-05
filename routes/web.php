<?php

use App\Http\Controllers\KeyboardController;
use Illuminate\Support\Facades\Route;

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
Route::get('/keyboard', [KeyboardController::class, 'index']);
Route::post('/keyboard/acquire-control', [KeyboardController::class, 'acquireControl']);
Route::post('/keyboard/release-control', [KeyboardController::class, 'releaseControl']);
Route::post('/keyboard/update-key-state', [KeyboardController::class, 'updateKeyState']);
Route::get('/keyboard/poll', [KeyboardController::class, 'poll']);
