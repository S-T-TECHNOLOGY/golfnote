<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::post('/login', [AuthController::class, 'loginAdmin']);
Route::group(['middleware' => ['assign.guard:admins','jwt.auth']], function () {
    Route::get('/golf/reservations', [AdminController::class, 'getReservationGolf']);
    Route::put('/golf/reservations/{id}', [AdminController::class, 'reservationGolfSuccess']);
    Route::get('/event/reservations', [AdminController::class, 'getReservationEvent']);
    Route::put('/event/reservations/{id}', [AdminController::class, 'reservationEventSuccess']);
    Route::get('/golfs', [AdminController::class, 'getGolfs']);
    Route::get('/events', [AdminController::class, 'getEvents']);
    Route::delete('/golfs/{id}', [AdminController::class, 'deleteGolf']);
    Route::post('/golf', [AdminController::class, 'createGolf']);
});