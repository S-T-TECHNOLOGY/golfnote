<?php


namespace App\Http\Controllers;


use App\Http\Services\MasterdataService;

class AppBaseController
{
    public function sendResponse($result, $message = 'success')
    {
        $res = [
            'message' => $message,
            'error_code' => 0,
            'data' => $result,
        ];

        return response()->json($res);
    }

    public function sendError($error, $code = 404)
    {
        $res = [
            'message' => $error,
        ];

        return response()->json($res, $code);
    }

}