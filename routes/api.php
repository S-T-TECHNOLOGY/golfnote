<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GolfCourseController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CityController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/cities', [CityController::class, 'getCities']);
Route::group(['middleware' => 'auth.jwt'], function () {
    Route::get('/user', [UserController::class, 'getUser']);
    Route::get('/golfs', [GolfCourseController::class, 'getGolfCourses']);
    Route::get('/golf/{id}', [GolfCourseController::class, 'getGolfCourseDetail']);
    Route::get('/events', [EventController::class, 'getAll']);
    Route::get('/event/{id}', [EventController::class, 'getEventDetail']);
});