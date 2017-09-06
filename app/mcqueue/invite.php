<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;
use App\service\exception\AllErrorException;
use Model\MarketingRedpactek;
/**
 * 新好友邀请活动 被邀请人完成定期首投 邀请人获得10红包
 * 监听定期投资事件
 * @pageroute
 */
function inviteredpacket(){

    // logs('记录复投发放体验金:' . PHP_EOL . var_export($_POST, true), 'redeliveryExperience');
    $userId = I('post.user_id', '', 'intval');//充值定期用户id
    $rechargeTime = I('post.time');//充值时间
    $rechargeAmount = I('post.amount');//充值金额
    $fromUserid = I('post.from_user_id');//邀请该用户的id

    $nodeName = 'frist_regular';//node name

    $activityName = 'invite';//新手活动名称
    $activityModel = new \Model\MarketingActivity();
    //获取活动开始、结束时间
    $usefulTime = $activityModel->getUsefulTimeByName($activityName);
    if(!$usefulTime) throw new Exception("no activate!", 7112);//没有找到活动数据
    if($rechargeTime < $usefulTime['start_time'] || $rechargeTime > $usefulTime['end_time'])
        throw new Exception("activate colsed!", 7112);

    //获取节点id
    $awardNode = new \Model\AwardNode();//活动节点
    $nodeId = $awardNode->getNode($nodeName);//获取节点id

    $marketingRedpactekModel = new \Model\MarketingRedpactek();
    $run = $marketingRedpactekModel->giveUserRedPacket($fromUserid,$userId,$nodeId);//发放红包

    //preSendRedPackToUser 请求用户中心接口
    unset($run['id']);
    unset($run['award']);//相关红包的配置参数
    $proPost = $run;
    $res = Common::jsonRpcApiCall((object)$proPost, 'preSendRedPackToUser', config('RPC_API.passport'));
    if($res['result']){
        //激活红包
        $activePost = [
            'uuid' => $run['uuid'],
        ];
        $activeRes = Common::jsonRpcApiCall((object)$activePost, 'activeRedPackToUser', config('RPC_API.passport'));
        if($activeRes['result']){
            $marketingRedpactekModel->changeRedPacketIsused($run['uuid'],1);
        }
    }else{
        throw new Exception("preSendRedPack false!", 7112);
    }

}

/**
 * 新好友邀请活动 每完成5个好友邀请 邀请人获得一张2加息券
 * 监听注册事件
 * @pageroute
 */
function invitecoupon(){
    $userId = I('post.user_id', '', 'intval');
    $time = I('post.time', '');
    $fromUserId = I('post.from_user_id', '', 'intval');
    $nodeName = 'frist_regular';//node name

    if($fromUserId == 0)
        exit("fromUserid null");
    $activityName = 'invite';//新手活动名称
    $activityModel = new \Model\MarketingActivity();
    //获取活动开始、结束时间
    $usefulTime = $activityModel->getUsefulTimeByName($activityName);
    if(!$usefulTime) throw new Exception("no activate!", 7112);//没有找到活动数据
    if($time < $usefulTime['start_time'] || $time > $usefulTime['end_time'])
        throw new Exception("activate colsed!", 7112);


    //请求用户中心获得邀请关系接口
    $getPost = [
        "userId" => $fromUserId,
    ];
    $getInviteUser = Common::jsonRpcApiCall((object)$getPost, 'getUserByFromId', config('RPC_API.passport'));
    var_dump($getInviteUser['result']['data']);
    if(empty($getInviteUser['result']['data'] ))
        throw new Exception("获取邀请关系数据异常!", 7112);
    if(count($getInviteUser['result']['data'] ) % 5 == 0){
        //发一张2%加息券
        $giveInterestcouponModel = new \Model\MarketingInterestcoupon();
        $giveInterestcouponModel->giveUserInterest();
        exit("55555");
    }

}



/**
 * 阶梯发加息劵
 */
function coupon($rechargeTime,$userId,$nodeId,$activate=true,$laterDays=0,$amount=''){
    $dateNow = $rechargeTime;
    $awardCoupon = new \Model\AwardInterestcoupon();//加息劵配置
    $operateCoupon = new \Model\MarketingInterestcoupon();
    try {
        $awardCouponInfo = $awardCoupon->filterUsefulInterestCoupon($nodeId);
        if(empty($awardCouponInfo)) throw new Exception("相关加息券未配置", 7112);

    } catch (Exception $e) {
        logs(['error' => $e->getCode(), 'message' => $e->getMessage()],"ladderScript");
    }
    try{
        $isExistCoupon = $operateCoupon->isExist($userId, $awardCouponInfo['id']);
        if(empty($awardCouponInfo)) throw new Exception("没有该用户的阶梯加息数据", 7112);
    } catch(Exception $e) {
        logs(['userId' => $userId, 'coupon' => $awardCouponInfo],"ladderScript");
    }

    //不存在，添加一张加息劵
    if(empty($isExistCoupon)){
        //*********************发放加息劵*********************
        $oneSourceId = getInfo('sourceId','ladder_percent_one');
        $top = $operateCoupon->isExist($userId,$oneSourceId);//存在1%的阶梯加息
        if($top) return true;//1%封顶
        $couponInfo = array(
            'id' => $awardCouponInfo['id'],
            'title' => $awardCouponInfo['title'],
            'rate' => $awardCouponInfo['rate'],
            'days' => $awardCouponInfo['days'],
            'laterDays' => $laterDays,
            'limit_desc' => $awardCouponInfo['limit_desc'],
        );

        $addCouponRes = $operateCoupon -> addLadderCouponForUser($userId,$couponInfo,$dateNow );
        //***************************************************
        //通知用户中心发放加息劵
        unset($addCouponRes['id']);
        $addCouponRes['type'] = 1;//阶梯加息特殊加息券
        $proPost = [
            'interestCoupon' => $addCouponRes,
        ];

        $rs = Common::jsonRpcApiCall((object)$proPost, 'preSendInterestCouponToUser', config('RPC_API.passport'));
        // $rs = true;
        if($rs && $activate){
            $activePost = [
                'uuid' => $addCouponRes['uuid'],
                'status' => 1, //激活为1
                'activeTime' => $addCouponRes['effective_start'],//开始计息时间
                'loseTime' => $addCouponRes['effective_end'],//计息结束时间
                'type' => 1,
            ];
            $rpcRes = Common::jsonRpcApiCall((object)$activePost, 'activateNewInterestCouponToUser', config('RPC_API.passport'));
            // $rpcRes['result'] = true;
            //update operate database  status
            logs($rpcRes,"ladder_percent_one");
            if(isset($rpcRes['result']) && $rpcRes['result']){
                //operate interestcpoupon 激活状态至为1
                $operateCoupon->updateActivate($addCouponRes['uuid']);
                logs("激活用户加息劵：".$addCouponRes['user_id'].PHP_EOL.$addCouponRes['uuid'],"activate_ladder_percent_one");
            }
        }

        return true;



    }else{
        // 1、是否有其他的加息劵
        $res = $operateCoupon->isOtherActivateExist($userId);//
        // var_dump($res);exit;
        $oneSourceId = getInfo('sourceId','ladder_percent_one');
        if(count($res) > 1 && $res[$isExistCoupon['id']]['source_id'] == $oneSourceId){
            //2、把这两张券直接只为失效
            //场景：全额提现后 再充值一次小于1w时，不作操作
            if(!empty($amount) && $amount<10000) return;
            //***************************************************
            foreach ($res as $key => $value) {
                # code...
                $disactivePost = [
                    'loseTime' => date("Y-m-d H:i:s"),
                    'token' => $value['uuid'],
                    'status'=> 0,
                ];
                if($value['rate'] == 0.5 && ($value['effective_start'] < date("Y-m-d H:i:s")) ) $disactivePost['status'] = 1;
                $rs = Common::jsonRpcApiCall((object)$disactivePost, 'disableInterestCouponToUser', config('RPC_API.passport'));
                // $rpcRes = true;
                if($rs){
                    $operateCoupon->updateActivate($disactivePost['token'],$disactivePost['status'],0);
                }else{
                    throw new AllErrorException(AllErrorException::PASSPORT_RETURN_ACTIVATE_HARF_FALSE, [], '用户中心返回激活失败0.5%');
                }
            }
            //3、立即再发一张1%的券
            /**********************************************************************************/
            $awardCoupon = new \Model\AwardInterestcoupon();//加息劵配置
            $getOneCoupon = $awardCoupon ->getCouponIdByName('ladder_basis_1');
            //var_dump($getOneCoupon);exit;
            $couponInfo = array(
                'id' => $getOneCoupon['id'],
                'title' => $getOneCoupon['title'],
                'rate' => $getOneCoupon['rate'],
                'days' => $getOneCoupon['days'],
                'laterDays' => 0,
                'limit_desc' => $getOneCoupon['limit_desc'],
            );

            $addCouponRes = $operateCoupon -> addLadderCouponForUser($userId,$couponInfo,$dateNow );
            //***************************************************
            //通知用户中心发放加息劵
            unset($addCouponRes['id']);
            $addCouponRes['type'] = 1;//阶梯加息特殊加息券
            $proPost = [
                'interestCoupon' => $addCouponRes,
            ];

            $rs = Common::jsonRpcApiCall((object)$proPost, 'preSendInterestCouponToUser', config('RPC_API.passport'));
            // $rs = true;
            if($rs && $activate){
                $activePost = [
                    'uuid' => $addCouponRes['uuid'],
                    'status' => 1, //激活为1
                    'activeTime' => $addCouponRes['effective_start'],//开始计息时间
                    'loseTime' => $addCouponRes['effective_end'],//计息结束时间
                    'type' => 1,
                ];
                $rpcRes = Common::jsonRpcApiCall((object)$activePost, 'activateNewInterestCouponToUser', config('RPC_API.passport'));
                // $rpcRes['result'] = true;
                //update operate database  status
                logs($rpcRes,"ladder_percent_one");
                if(isset($rpcRes['result']) && $rpcRes['result']){
                    //operate interestcpoupon 激活状态至为1
                    $operateCoupon->updateActivate($addCouponRes['uuid']);
                    logs("激活用户加息劵：".$addCouponRes['user_id'].PHP_EOL.$addCouponRes['uuid'],"activate_ladder_percent_one");
                }
            }

            return true;
            /**********************************************************************************/
        }
    }


}

/**
 * 获取相关配置数据
 * getInfo('sourceId','ladder_percent_one')
 * getInfo('sourceId','ladder_percent_half_keep')
 **/
function getInfo($search,$data){
    //search:nodeId,sourceId
    if(empty($search) && empty($data) ){
        throw new Exception("获取相关数据时，参数异常", 7112);
    }
    $awardNode = new \Model\AwardNode();//活动节点
    $awardCoupon = new \Model\AwardInterestcoupon();//加息劵配置

    $resNodeId = $awardNode->getNode($data);
    if(empty($resNodeId))
        throw new Exception("未获取到活动节点id", 7112);

    $resSourceId = $awardCoupon->filterUsefulInterestCoupon($resNodeId);
    if(empty($resSourceId))
        throw new Exception("未获取到sourceId", 7112);

    if($search == "nodeId"){
        return $resNodeId;
    }elseif($search == "sourceId"){
        return $resSourceId['id'];
    }

}



