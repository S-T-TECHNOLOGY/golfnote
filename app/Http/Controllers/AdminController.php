<?php

namespace App\Http\Controllers;

use App\Constants\UserScoreImageStatus;
use App\Errors\ScoreImageErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Requests\AdminHandleScoreImageRequest;
use App\Http\Requests\AdminPushNotificationRequest;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\CreateGolfRequest;
use App\Http\Requests\CreateMarketRequest;
use App\Http\Requests\CreateQuestionRequest;
use App\Http\Requests\UploadImageRequest;
use App\Models\UserScoreImage;
use App\Services\AdminService;
use App\Services\ScoreService;
use Illuminate\Http\Request;

class AdminController extends AppBaseController
{
    protected $adminService;
    protected $scoreService;
    public function __construct(AdminService $adminService, ScoreService $scoreService)
    {
        $this->adminService = $adminService;
        $this->scoreService = $scoreService;
    }

    public function getReservationGolf(Request $request)
    {
        $params = $request->all();
        $data = $this->adminService->getReservationGolf($params);
        return $this->sendResponse($data);
    }

    public function reservationGolfSuccess($id)
    {
        $data = $this->adminService->reservationGolfSuccess($id);
        return $this->sendResponse($data);
    }

    public function getReservationEvent(Request $request)
    {
        $params = $request->all();
        $data = $this->adminService->getReservationEvent($params);
        return $this->sendResponse($data);
    }

    public function reservationEventSuccess($id)
    {
        $data = $this->adminService->reservationEventSuccess($id);
        return $this->sendResponse($data);
    }

    public function getGolfs(Request $request)
    {
        $params = $request->all();
        $data = $this->adminService->getGolfs($params);
        return $this->sendResponse($data);
    }

    public function getGolfDetail($id)
    {
        $data = $this->adminService->getGolfDetail($id);
        return $this->sendResponse($data);
    }

    public function deleteGolf($id)
    {
        $data = $this->adminService->deleteGolf($id);
        return $this->sendResponse($data);
    }

    public function createGolf(CreateGolfRequest $request)
    {
        $params = $request->all();
        $data = $this->adminService->createGolf($params);
        return $this->sendResponse($data);
    }

    public function getEvents(Request $request)
    {
        $params = $request->all();
        $data = $this->adminService->getEvents($params);
        return $this->sendResponse($data);
    }

    public function getEventDetail($id)
    {
        $data = $this->adminService->getEventDetail($id);
        return $this->sendResponse($data);
    }

    public function getQuestions(Request $request)
    {
        $params = $request->all();
        $data = $this->adminService->getQuestions($params);
        return $this->sendResponse($data);
    }

    public function getQuestionDetail($id)
    {
        $data = $this->adminService->getQuestionDetail($id);
        return $this->sendResponse($data);
    }

    public function createQuestion(CreateQuestionRequest $request)
    {
        $params = $request->all();
        $data = $this->adminService->createQuestion($params);
        return $this->sendResponse($data);
    }

    public function editQuestion(CreateQuestionRequest $request, $id)
    {
        $params = $request->all();
        $params['id'] = $id;
        $data = $this->adminService->editQuestion($params);
        return $this->sendResponse($data);
    }

    public function deleteQuestion($id)
    {
        $data = $this->adminService->deleteQuestion($id);
        return $this->sendResponse($data);
    }

    public function deleteEvent($id)
    {
        $data = $this->adminService->deleteEvent($id);
        return $this->sendResponse($data);
    }

    public function uploadImage(UploadImageRequest $request)
    {
        $data = $this->adminService->uploadImage($request->all());
        return $this->sendResponse($data);
    }

    public function getUsers(Request $request)
    {
        $data = $this->adminService->getUsers($request->all());
        return $this->sendResponse($data);
    }

    public function createEvent(CreateEventRequest $request)
    {
        $data = $this->adminService->createEvent($request->all());
        return $this->sendResponse($data);
    }

    public function editEvent(CreateEventRequest $request, $id)
    {
        $params = $request->all();
        $params['id'] = $id;
        $data = $this->adminService->editEvent($params);
        return $this->sendResponse($data);
    }

    public function getScoreImages(Request $request)
    {
        $data = $this->adminService->getScoreImages($request->all());
        return $this->sendResponse($data);
    }

    public function getScoreImageDetail($id)
    {
        $data = $this->adminService->getScoreImageDetail($id);
        return $this->sendResponse($data);
    }

    public function getMarkets(Request $request)
    {
        $data = $this->adminService->getMarkets($request->all());
        return $this->sendResponse($data);
    }

    public function getMarketDetail($id)
    {
        $data = $this->adminService->getMarketDetail($id);
        return $this->sendResponse($data);
    }

    public function getOldMarkets(Request $request)
    {
        $data = $this->adminService->getOldMarkets($request->all());
        return $this->sendResponse($data);
    }

    public function createMarket(CreateMarketRequest $request)
    {
        $data = $this->adminService->createMarket($request->all());
        return $this->sendResponse($data);
    }

    public function editMarket(CreateMarketRequest $request, $id)
    {
        $params = $request->all();
        $params['id'] = $id;
        $data = $this->adminService->editMarket($params);
        return $this->sendResponse($data);
    }


    public function deleteMarket($id)
    {
        $data = $this->adminService->deleteMarket($id);
        return $this->sendResponse($data);
    }

    public function deleteOldMarket($id)
    {
        $data = $this->adminService->deleteOldMarket($id);
        return $this->sendResponse($data);
    }

    public function pushNotification(AdminPushNotificationRequest $request)
    {
        $data = $this->adminService->pushNotification($request->all());
        return $this->sendResponse($data);
    }

    public function handleScoreImage(AdminHandleScoreImageRequest $request, $id)
    {
        $scoreImage = UserScoreImage::where('id', $id)->first();
        if (!$scoreImage) {
            throw new BusinessException('Không tìm thấy phiếu điểm', ScoreImageErrorCode::SCORE_IMAGE_NOT_FOUND);
        }

        $params = $request->all();
        $params['id'] = $scoreImage->room_id;
        $data = $this->scoreService->calculateScore($params, true);
        $scoreImage->status = UserScoreImageStatus::COMPLETED_STATUS;
        $scoreImage->save();

        return $this->sendResponse($data);
    }

    public function getAdminNotifications(Request $request)
    {
        $data = $this->adminService->getAdminNotifications($request->all());
        return $this->sendResponse($data);
    }

    public function pushAllUserByTemplateNotification($id)
    {
        $data = $this->adminService->pushAllUserByTemplateNotification($id);
        return $this->sendResponse($data);
    }
}
