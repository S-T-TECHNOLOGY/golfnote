<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GolfController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\OldThingController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GolfHoleController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\UserFriendController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\NotificationController;
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
Route::post('/forgot_pass', [AuthController::class, 'forgotPassword']);
Route::get('/golf_hole/{type}', [GolfHoleController::class, 'getHoleByType']);
Route::group(['middleware' => ['assign.guard:users','jwt.auth']], function () {
    Route::get('/user', [UserController::class, 'getUser']);
    Route::get('/banners', [BannerController::class, 'getBanner']);
    Route::get('/ranking', [RankingController::class, 'getRanking']);
    Route::post('/banners', [BannerController::class, 'create']);
    Route::get('/user', [UserController::class, 'getUser']);
    Route::put('/user/profile', [UserController::class, 'editProfile']);
    Route::get('/user/score/histories', [ScoreController::class, 'history']);
    Route::get('/users', [UserController::class, 'find']);
    Route::get('/golfs', [GolfController::class, 'getGolfs']);
    Route::post('/golf/{id}/courses', [GolfController::class, 'getGolfCourses']);
    Route::get('/clubs', [ClubController::class, 'getAll']);
    Route::get('/golf/{id}', [GolfController::class, 'getGolfCourseDetail']);
    Route::get('/events', [EventController::class, 'getAll']);
    Route::get('/event/{id}', [EventController::class, 'getEventDetail']);
    Route::get('/old_things', [OldThingController::class, 'getAll']);
    Route::get('/old_thing/{id}', [OldThingController::class, 'getDetail']);
    Route::get('/markets', [MarketController::class, 'getAll']);
    Route::get('/market/{id}', [MarketController::class, 'getDetail']);
    Route::post('/room', [RoomController::class, 'createRoom']);
    Route::get('/room/{id}', [RoomController::class, 'getRoomDetail']);
    Route::post('/room/{id}/score', [ScoreController::class, 'calculateScore']);
    Route::post('/room/{id}/draft-score', [ScoreController::class, 'logDraftScore']);
    Route::post('/user/reservation', [UserController::class, 'reservationGolf']);
    Route::post('/user/reservation-event', [UserController::class, 'reservationEvent']);
    Route::post('/user/old-thing', [UserController::class, 'sellOldThing']);
    Route::post('/user/club', [UserController::class, 'createClub']);
    Route::get('/user/room-playing', [UserController::class, 'getRoomPlaying']);
    Route::get('/user/notifications', [NotificationController::class, 'getAll']);
    Route::put('/user/password', [UserController::class, 'changePassword']);
    Route::post('/user/friend', [UserFriendController::class, 'addFriend']);
    Route::put('/user/friend/accept', [UserFriendController::class, 'acceptRequest']);
    Route::put('/user/friend/cancel', [UserFriendController::class, 'cancelRequest']);
    Route::put('/logout', [UserController::class, 'logout']);
});