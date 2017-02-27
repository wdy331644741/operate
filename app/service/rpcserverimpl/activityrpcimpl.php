<?php

namespace App\service\rpcserverimpl;

use Storage\Storage;
use App\service\exception\AllErrorException;

class ActivityRpcImpl extends BaseRpcImpl {

    /**
     * banner 图
     *
     * @JsonRpcMethod
     */
    public function banners()
    {
        //获取当前有效banner图
        $storage = new Storage();
        $bannerModel = new \Model\MarketingBanner();
        $bannerList = $bannerModel->activedBanners();

        foreach ($bannerList as $key => $banner) {
            $bannerList[ $key ]['img_url'] = $storage->getViewUrl($banner['img_url']);
        }

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $bannerList
        );
    }

    /**
     * 活动列表
     *
     * @JsonRpcMethod
     */
    public function activities()
    {
        //获取当前有效banner图
        $storage = new Storage();
        $activityModel = new \Model\MarketingActivity();
        $activities = $activityModel->activityList();

        foreach ($activities as $key => $activity) {
            $activities[ $key ]['img_url'] = $storage->getViewUrl($activity['img_url']);
        }

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $activities
        );
    }

    /**
     * 系统公告
     *
     * @JsonRpcMethod
     */
    public function noticeList()
    {
        $acticleModel = new \Model\MarketingArticle();
        $noticeList = $acticleModel->noticeList();
        foreach ($noticeList as $key => $notice) {
            $noticeList[ $key ]['content'] = htmlspecialchars_decode($noticeList[ $key ]['content']);
            $noticeList[ $key ]['link'] = 'https://php1.wanglibao.com/app/bulletin/detail/3';
        }

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $noticeList
        );
    }
}
