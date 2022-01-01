<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\CreateGolfRequest;
use App\Http\Requests\CreateQuestionRequest;
use App\Http\Requests\UploadImageRequest;
use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminController extends AppBaseController
{
    protected $adminService;
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
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

    public function getQuestions(Request $request)
    {
        $params = $request->all();
        $data = $this->adminService->getQuestions($params);
        return $this->sendResponse($data);
    }

    public function createQuestion(CreateQuestionRequest $request)
    {
        $params = $request->all();
        $data = $this->adminService->createQuestion($params);
        return $this->sendResponse($data);
    }

    public function deleteQuestion($id)
    {
        $data = $this->adminService->deleteQuestion($id);
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
}
