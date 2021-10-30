<?php

namespace App\Http\Controllers;

use JWTAuth;

class UserController extends AppBaseController
{
    public function getUser() {
        $user = JWTAuth::user();
        return $this->sendResponse($user);
    }
}
