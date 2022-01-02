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
    Route::delete('/events/{id}', [AdminController::class, 'deleteEvent']);
    Route::delete('/golfs/{id}', [AdminController::class, 'deleteGolf']);
    Route::post('/golf', [AdminController::class, 'createGolf']);
    Route::get('/questions', [AdminController::class, 'getQuestions']);
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::delete('/questions/{id}', [AdminController::class, 'deleteQuestion']);
    Route::post('/questions', [AdminController::class, 'createQuestion']);
    Route::post('/images', [AdminController::class, 'uploadImage']);
    Route::get('/score/images', [AdminController::class, 'getScoreImages']);
    Route::get('/score/images/{id}', [AdminController::class, 'getScoreImageDetail']);
    Route::get('/markets', [AdminController::class, 'getMarkets']);
    Route::post('/markets', [AdminController::class, 'createMarket']);
    Route::delete('/markets/{id}', [AdminController::class, 'deleteMarket']);
    Route::post('/events', [AdminController::class, 'createEvent']);
});