<?php

namespace App\service\rpcserverimpl;

use Storage\Storage;
use App\service\exception\AllErrorException;

class ActivityRpcImpl extends BaseRpcImpl
{

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
            $bannerList[$key]['img_url'] = $storage->getViewUrl($banner['img_url']);
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
    public function activities($params)
    {
        $dateNow = date('Y-m-d H:i:s',time());
        //验证
        if (empty($params->page)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }
        //获取当前有效banner图
        $storage = new Storage();
        $activityModel = new \Model\MarketingActivity();
        $activities = $activityModel->activityList($params->page);

        // var_export($activities);exit;
        $pagecounts = ceil(count($activities)/5);
        foreach ($activities as $key => $activity) {
            $activities[$key]['img_url'] = $storage->getViewUrl($activity['img_url']);
            
            if($activity['start_time'] < $dateNow && $activity['end_time'] < $dateNow){
                // $activities[$key]['status'] = '-1';//not begin  //已过期
                $activity['status'] = '-1';
                $pastAactivities[] = $activity;
            }elseif($activity['start_time'] > $dateNow && $activity['end_time'] > $dateNow){
                //$activities[$key]['status'] = '0';//past //未开始
                $activity['status'] = '0';
                $unbeginAactivities[] = $activity;

            }elseif($activity['start_time'] < $dateNow && $activity['end_time'] > $dateNow){
                // $activities[$key]['status'] = '1';//正在进行的
                $activity['status'] = '1';
                $ingAactivities[] = $activity;
            }
            
        }
        if(isset($pastAactivities) && !empty($pastAactivities)){
            $res = array_merge($ingAactivities,$pastAactivities);
        }else{
            $res = $ingAactivities;
        }

        $arrayStart = ($params->page -1)*5;
        $fin = array_slice($res,$arrayStart,5);

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $fin,
            'pagecounts' => $pagecounts,
        );
    }

    /**
     * 系统公告
     *
     * @JsonRpcMethod
     */
    public function noticeList($params)
    {
        //验证是否有page;
        if (empty($params->page)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        $acticleModel = new \Model\MarketingArticle();
        $noticeList = $acticleModel->noticeList($params->page);
        foreach ($noticeList as $key => $notice) {
            $noticeList[$key]['content'] = htmlspecialchars_decode($noticeList[$key]['content']);
            $noticeList[$key]['link'] = 'https://php1.wanglibao.com/app/bulletin/detail/3';
        }

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $noticeList
        );
    }
}
