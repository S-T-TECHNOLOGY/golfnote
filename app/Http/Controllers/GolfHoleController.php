<?php

namespace App\Http\Controllers;

use App\Models\GolfHole;

class GolfHoleController extends AppBaseController
{
    public function getHoleByType($type)
    {
        $golfHoles = GolfHole::select('number_hole', 'standard')->where('type', $type)->get();
        return $this->sendResponse($golfHoles);
    }
}
