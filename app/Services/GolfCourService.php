<?php


namespace App\Services;


use App\Constants\Consts;
use App\Errors\GolfCourseErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\GolfCollection;
use App\Http\Resources\GolfResource;
use App\Models\Golf;
use App\Models\GolfHole;
use App\Models\HoleImage;

class GolfCourService
{
    public function getGolfs($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $golfCourses = Golf::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%'.$key.'%' );
        })->orderBy('id', 'desc')->paginate($limit);

        return new GolfCollection($golfCourses);
    }

    public function getGolfCourseDetail($id)
    {
        $golfCourse = Golf::where('id', $id)->first();
        if (!$golfCourse) {
            throw new BusinessException('Không tìm thấy sân golf', GolfCourseErrorCode::GOLF_COURSE_NOT_FOUND);
        }

        return new GolfResource($golfCourse);
    }

    public function getGolfCourses($params)
    {
        $golfCourses = [];
        foreach ($params['courses'] as $course) {
            $courses = HoleImage::select('image', 'course', 'number_hole')->where('golf_id', $params['id'])->where('course', $course)->get();
            $golfCourses = array_merge($golfCourses, $courses->toArray());
        }

        return $golfCourses;
    }
}