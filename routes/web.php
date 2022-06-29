<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ActiveAccountController;
use App\Http\Controllers\HomController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/confirm-email', [ActiveAccountController::class, 'activate']);
Route::get('/about', [HomController::class, 'about']);
Route::get('/golfnote/{file}', [HomController::class, 'previewFile']);
