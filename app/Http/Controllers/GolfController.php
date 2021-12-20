<?php


namespace App\Http\Controllers;


use App\Http\Requests\CreateGolfRequest;
use App\Http\Requests\GolfCourseRequest;
use App\Services\GolfCourService;
use Illuminate\Http\Request;


class GolfController extends AppBaseController
{
    protected $golfCourseService;

    public function __construct(GolfCourService $golfCourService)
    {
        $this->golfCourseService = $golfCourService;
    }

    public function getGolfs(Request $request)
    {
        $data = $this->golfCourseService->getGolfs($request->all());
        return $this->sendResponse($data);
    }

    public function getGolfCourseDetail($id)
    {
        $golfCourse = $this->golfCourseService->getGolfCourseDetail($id);
        return $this->sendResponse($golfCourse);
    }

    public function getGolfCourses(GolfCourseRequest $request, $id)
    {
        $params = $request->all();
        $params['id'] = $id;
        $golfCourse = $this->golfCourseService->getGolfCourses($params);
        return $this->sendResponse($golfCourse);
    }

    public function createGolf(CreateGolfRequest $request)
    {
        $params = $request->all();
        $data = $this->golfCourseService->createGolf($params);
        return $this->sendResponse($data);
    }

}