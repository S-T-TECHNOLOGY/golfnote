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
    Route::get('/events/{id}', [AdminController::class, 'getEventDetail']);
    Route::put('/events/{id}', [AdminController::class, 'editEvent']);
    Route::delete('/events/{id}', [AdminController::class, 'deleteEvent']);
    Route::delete('/golfs/{id}', [AdminController::class, 'deleteGolf']);
    Route::post('/golf', [AdminController::class, 'createGolf']);
    Route::get('/questions', [AdminController::class, 'getQuestions']);
    Route::get('/questions/{id}', [AdminController::class, 'getQuestionDetail']);
    Route::put('/questions/{id}', [AdminController::class, 'editQuestion']);
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::delete('/questions/{id}', [AdminController::class, 'deleteQuestion']);
    Route::post('/questions', [AdminController::class, 'createQuestion']);
    Route::post('/images', [AdminController::class, 'uploadImage']);
    Route::get('/score/images', [AdminController::class, 'getScoreImages']);
    Route::get('/score/images/{id}', [AdminController::class, 'getScoreImageDetail']);
    Route::post('/score/images/{id}', [AdminController::class, 'handleScoreImage']);
    Route::get('/markets', [AdminController::class, 'getMarkets']);
    Route::get('/markets/{id}', [AdminController::class, 'getMarketDetail']);
    Route::put('/markets/{id}', [AdminController::class, 'editMarket']);
    Route::get('/old/markets', [AdminController::class, 'getOldMarkets']);
    Route::delete('/old/markets/{id}', [AdminController::class, 'deleteOldMarket']);
    Route::post('/markets', [AdminController::class, 'createMarket']);
    Route::delete('/markets/{id}', [AdminController::class, 'deleteMarket']);
    Route::post('/events', [AdminController::class, 'createEvent']);
    Route::post('/notifications', [AdminController::class, 'pushNotification']);
});