<?php

namespace App\service\rpcserverimpl;

use Storage\Storage;
//use Lib\UserData;
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
     * app_index  移动端手机展示
     *
     * @JsonRpcMethod
     */
    public function getIndexSlogan()
    {
        //获取当前有效slogan
        // $storage = new Storage();
        $indexModel = new \Model\MarketingIndex();
        $activity_define = $indexModel->getMomentSlogen('activity');//获取定义的展示文案
        $notice_define = $indexModel->getMomentSlogen('notice');//获取定义的展示文案
        $default = $indexModel->getDefaultSlogen();//获取默认的展示文案

        //优先展示定义的文案
        //活动》公告》日常
        if(!empty($activity_define)){
            // if(count($activity_define)>1)
            $resData = [
                'title' => $activity_define['title'],
                'link_url' => $activity_define['link_url'],
                'display_name' => $activity_define['display_name'],
                'check_login' => $activity_define['check_login'],
            ];
        }elseif(!empty($notice_define)){
            $resData = [
                'title' => $notice_define['title'],
                'link_url' => $notice_define['link_url'],
                'display_name' => $notice_define['display_name'],
                'check_login' => $notice_define['check_login'],
            ];
        }else{
            $resData = [
                'title' => $default['title'],
                'link_url' => $default['link_url'],
                'display_name' => $default['display_name'],
                'check_login' => $default['check_login'],
            ];
        }
        // $resData = [
        //     'title' => $default['title'],
        //     'link_url' => $default['link_url'],
        //     'display_name' => $default['display_name'],
        // ];
        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $resData
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

        $pastAactivities = $ingAactivities = [];
        $pagecounts = ceil(count($activities)/5);
        foreach ($activities as $key => $activity) {
            $activity['img_url'] = $storage->getViewUrl($activity['img_url']);

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

        if(!empty($pastAactivities && !empty($ingAactivities))){
            $res = array_merge($ingAactivities,$pastAactivities);
        }elseif(empty($ingAactivities) && !empty($pastAactivities)){
            $res = $pastAactivities;
        }elseif(empty($pastAactivities) && !empty($ingAactivities)){
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
        if($params->tiao != 'pc'){
            //检查登录状态
            if (($this->userId = $this->checkLoginStatus()) === false) {
                throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
            }
        }
        //验证是否有page;
        if (empty($params->page)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        $articleType = isset($params->type) ? $params->type : 'notice';

        $acticleModel = new \Model\MarketingArticle();
        $acticleLogModel = new \Model\MarketingArticleLog();

        $isRead = $acticleLogModel->isReadByUser($this->userId);

        
        if(isset($isRead) && !empty($isRead)){
            $readArray = array_column($isRead,'counts','article_id');
        }

        $datacounts = $acticleModel->getCount($articleType);
        $noticeList = $acticleModel->noticeList($params->page,$articleType);
        // var_export($readArray);
        // var_export($noticeList);
        // exit;
        $storage = new Storage();
        foreach ($noticeList as $key => $notice) {
            // $noticeList[$key]['content'] = htmlspecialchars_decode($noticeList[$key]['content']);
            //$noticeList[$key]['link'] = 'https://php1.wanglibao.com/app/bulletin/detail/3';
            $noticeList[$key]['img_url'] = $storage->getViewUrl($notice['img_url']);
            $noticeList[$key]['readCounts'] = isset($readArray[$notice['id']])?(int)$readArray[$notice['id']]:0;
        }
        $pageCount = ceil($datacounts / 10);
        if($articleType=='article'){
            $pageCount = ceil($datacounts / 5);
        }


        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $noticeList,
            'pagecounts' =>$pageCount
        );
    }

    /**
     * 系统公告内容
     *
     * @JsonRpcMethod
     */
    public function noticeContent($params){
        if($params->tiao != 'pc'){
            //验证
            //检查登录状态
            if (($this->userId = $this->checkLoginStatus()) === false) {
                throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
            }
        }

        if (empty($params->article_id)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        $acticleLogModel = new \Model\MarketingArticleLog();
        $acticleContentModel = new \Model\MarketingArticle();


        $acticleContent = $acticleContentModel->getActicle($params->article_id);

        $isRead = $acticleLogModel->isReadByUser($this->userId,$params->article_id);
        // var_export($isRead);exit;
        if($params->tiao != 'pc'){
            //添加一条阅读记录
            //更新、累加记录
            if(isset($isRead) && empty($isRead)){
                $res = $acticleLogModel->addReadLog($params->article_id,$this->userId);
            }else{
                $acticleLogModel->updateReadLog($params->article_id,$this->userId,$isRead[0]['counts']);
            }
        }
        $test = htmlspecialchars_decode($acticleContent[0]['content']);
        // return $test;
        //$test = strip_tags($test);
        return array(
            'code'      => 0,
            // 'data'   => $acticleContent[0]['content'],
            'data' => $test,
            // 'userid'    => $this->userId,
            // 'message'   => 'success',
        );
    }

    /**
     * 检查用户是否有未读公告
     *
     * @JsonRpcMethod
     */
    public function haveNewNotice(){
        //验证
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }
        $articleModel = new \Model\MarketingArticle();


        // return $articleModel->haveUnreadActicle($this->userId);
        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $articleModel->haveUnreadActicle($this->userId)
        );

    }
}
