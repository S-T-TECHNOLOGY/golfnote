<?php


namespace App\Http\Controllers;


use App\Models\City;

class CityController extends AppBaseController
{
    public function getCities()
    {
        $cities = City::select('name')->get();
        return $this->sendResponse($cities);
    }
}