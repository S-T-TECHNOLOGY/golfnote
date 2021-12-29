<?php

namespace App\Http\Controllers;

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
}
