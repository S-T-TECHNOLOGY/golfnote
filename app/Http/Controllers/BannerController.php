<?php

namespace App\Http\Controllers;

use App\Constants\BannerType;
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
        $banner = Banner::select('id', 'image', 'link', 'title', 'content')->where('type', BannerType::BANNER_TYPE)->orderBy('created_at', 'desc')->first();
        return $this->sendResponse($banner);
    }

    public function getBanners()
    {
        $banners = Banner::select('id', 'image', 'link', 'title', 'content', 'type')->orderBy('created_at', 'desc')->get();
        return $this->sendResponse($banners);
    }

}
