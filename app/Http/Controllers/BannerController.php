<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBannerRequest;
use App\Models\Banner;
use App\Services\BannerService;

class BannerController extends AppBaseController
{
    protected $bannerService;
    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    public function create(CreateBannerRequest $request)
    {
        $banner = $this->bannerService->create($request->all());
        return $this->sendResponse($banner);
    }

    public function getBanner()
    {
        $now = date('Y-m-d');
        $banner = Banner::select('id', 'image', 'link', 'title', 'content')->where('expired_date', '>=', $now)->first();
        return $this->sendResponse($banner);
    }

}
