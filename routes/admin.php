<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::post('/login', [AuthController::class, 'loginAdmin']);
Route::group(['middleware' => ['assign.guard:admins','jwt.auth']], function () {
    Route::get('/golf/reservations', [AdminController::class, 'getReservationGolf']);
    Route::put('/golf/reservations/{id}', [AdminController::class, 'reservationGolfSuccess']);
});