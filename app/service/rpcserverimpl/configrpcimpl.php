<?php

namespace App\service\rpcserverimpl;

//use Storage\Storage;
//use Lib\UserData;
use App\service\exception\AllErrorException;

class ConfigRpcImpl extends BaseRpcImpl
{

    /**
     *
     * @JsonRpcMethod
     */
    public function config($params)
    {
        //验证
        if (empty($params->key)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }
        $configModel = new \Model\GloabConfig();
        $dbData = $configModel->getConfigByKey($params->key);
        if(!$dbData){
            throw new AllErrorException(AllErrorException::GET_CONF_EMPTY);
        }
        foreach ($dbData as &$value) {
            # code...
            $value['value'] = json_decode($value['value'],TRUE);
        }
        unset($value);
        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $dbData,
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
