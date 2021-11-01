<?php


namespace App\Services;


use App\Constants\Consts;
use App\Errors\GolfCourseErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\GolfCourseCollection;
use App\Http\Resources\GolfCourseResource;
use App\Models\GolfCourse;

class GolfCourService
{
    public function getGolfCourse($params) {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $golfCourses = GolfCourse::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%'.$key.'%' );
        })->paginate($limit);

        return new GolfCourseCollection($golfCourses);
    }

    public function getGolfCourseDetail($id) {
        $golfCourse = GolfCourse::where('id', $id)->first();
        if (!$golfCourse) {
            throw new BusinessException('Không tìm thấy sân golf', GolfCourseErrorCode::GOLF_COURSE_NOT_FOUND);
        }

        return new GolfCourseResource($golfCourse);
    }
}