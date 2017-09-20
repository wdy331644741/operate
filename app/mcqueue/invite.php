<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;
use App\service\exception\AllErrorException;
use Model\MarketingRedpactek;
use App\service\rpcserverimpl\SendCouponRpcImpl;
/**
 * 新好友邀请活动 被邀请人完成定期首投 邀请人获得10红包
 * 监听定期投资事件
 * @pageroute
 */
function inviteredpacket(){

    $userId = I('post.user_id', '', 'intval');//充值定期用户id
    $rechargeTime = I('post.buy_time');//充值时间
    $rechargeAmount = I('post.amount');//充值金额
    $fromUserid = I('post.from_id');//邀请该用户的id
    $demand_count = I('post.demand_count');//投资定期次数

    if($demand_count>1)
        exit("不是首次定期投资");
    if($fromUserid == 0)
        exit("没有邀请人id");
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
        exit("发送成功");
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
    $countNum = I('post.count', '', 'intval');
    $nodeName = 'five_invite_coupon';//node name

    if($fromUserId == 0 || $countNum == 0)
        exit("fromUserid null");
    $activityName = 'invite';//新手活动名称
    $activityModel = new \Model\MarketingActivity();
    //获取活动开始、结束时间
    $usefulTime = $activityModel->getUsefulTimeByName($activityName);
    if(!$usefulTime) throw new Exception("no activate!", 7112);//没有找到活动数据
    if($time < $usefulTime['start_time'] || $time > $usefulTime['end_time'])
        throw new Exception("activate colsed!", 7112);

    //获取节点id
    $awardNode = new \Model\AwardNode();//活动节点
    $nodeId = $awardNode->getNode($nodeName);//获取节点id

    //取redis中 邀请活动每邀请n个人  送一张加息券
    $redis = getReidsInstance();
    $activityInfo = $redis->hget('operate_gloab_conf',$activityName);
    logs($activityInfo,"invite_redis");
    $activityInfo = json_decode($activityInfo,true);
    $eachSend = $activityInfo['invite_each_coupon'];
    //请求用户中心获得邀请关系接口
    // $getPost = [
    //     "userId" => $fromUserId,
    // ];
    // $getInviteUser = Common::jsonRpcApiCall((object)$getPost, 'getUserByFromId', config('RPC_API.passport'));
    // if(empty($getInviteUser['result']['data'][$fromUserId]['list'] ))
    //     throw new Exception("获取邀请关系数据异常!", 7112);
    //并发注册时 bug
    // $invites = count($getInviteUser['result']['data'][$fromUserId]['list'] );//12个
    $invites = $countNum;
    $willGiveCount = floor($invites/$eachSend);
    //查找该用户名下已经存在的加息券数量
    //获取到该活动相关的加息券sourceId
    $inviteCouponSourceId = getInfo('sourceId','five_invite_coupon');
    $couponModel = new \Model\MarketingInterestcoupon();
    $allreadyHave = $couponModel->isOtherActivateExist($fromUserId,$inviteCouponSourceId);
    // var_dump($allreadyHave);exit;
    $allreadyHave = count($allreadyHave);//已经发了多少张加息券

    logs($fromUserId."->".$countNum, "invite_counts");
    logs($fromUserId."willGiveCount->".$willGiveCount."-----allreadyHave->".$allreadyHave, "coupon_status");
    //补几张并且  不等于5的倍数时
    // if(count($getInviteUser['result']['data'][$fromUserId]['list'] ) % $eachSend != 0){
        
    // }
    
    /**
    //9.14再次修改  不要判断了。前提是用户中心推过来的消息，一个都不漏
    if($invites % $eachSend == 0){
        //发一张2%加息券  直接发放没有什么逻辑，直接调用手动发放奖品rpc
        // $giveInterestcouponModel = new \Model\MarketingInterestcoupon();
        // $giveInterestcouponModel->giveUserInterest();
        // exit("55555");
        $send = new SendCouponRpcImpl();
        $sendRes = $send->activitySendAction(1, $fromUserId, $nodeId);
        exit("发送加息券成功");
    }else{
        for ($i=0; $i < $willGiveCount-$allreadyHave; $i++) { 
            # code...
            $send = new SendCouponRpcImpl();
            $sendRes = $send->activitySendAction(1, $fromUserId, $nodeId);
            
            exit("补发加息券");
        }
    }
    **/

    //9.14再次修改  不要判断了。前提是用户中心推过来的消息，一个都不漏
    if($invites % $eachSend == 0){
        //发一张2%加息券  直接发放没有什么逻辑，直接调用手动发放奖品rpc
        // $giveInterestcouponModel = new \Model\MarketingInterestcoupon();
        // $giveInterestcouponModel->giveUserInterest();
        // exit("55555");
        $send = new SendCouponRpcImpl();
        $sendRes = $send->activitySendAction(1, $fromUserId, $nodeId);
        exit("发送加息券成功");
    }

}


/**
 * 新好友邀请活动 被邀请人福利：完成首次投资即可获得10000元体验金，体验时间为1天
 * 监听充值事件
 * @pageroute
 */
function inviterecharge(){
    $userId = I('post.user_id', '', 'intval');//用户id
    $rechargeTime = I('post.time');//充值时间
    $rechargeAmount = I('post.amount');//充值金额
    $rechargeAmountTotal = I('post.total');//累计本金
    $rechargeOrderId = I('post.order_id');//累计本金

    if(empty($rechargeOrderId)){
        exit("虚伪的充值事件");
    }
    $nodeName = 'bing_invite_tenTthonsend_exp';//node name

    $activityName = 'invite';//新手活动名称

    //1 判断在此之前 充值次数
    $postParams = array(
            'userId'     => $userId,
            'startTime'  => '',//活动开始时间
            'endTime'    => $rechargeTime,
            'status'     => 200,
        );
    $rechargeTimes = Common::jsonRpcApiCall((object)$postParams, 'getRechargeRecords', config('RPC_API.passport'));
    // $rechargeTimes = 2;
    if(count($rechargeTimes['result']) > 1) return "充值次数".count($rechargeTimes['result']);// 需要是首投

    $activityModel = new \Model\MarketingActivity();
    //获取活动开始、结束时间
    $usefulTime = $activityModel->getUsefulTimeByName($activityName);
    if(!$usefulTime) throw new Exception("no activate!", 7112);//没有找到活动数据
    if($rechargeTime < $usefulTime['start_time'] || $rechargeTime > $usefulTime['end_time'])
        throw new Exception("activate colsed!", 7112);

    //获取节点id
    $awardNode = new \Model\AwardNode();//活动节点
    $nodeId = $awardNode->getNode($nodeName);//获取节点id


    //请求用户中心 该用户注册相关数据
    $post = [
        'userId' => $userId,
        'params' => 'from_user_id,create_time',
    ];
    $userInfo = Common::jsonRpcApiCall((object)$post, '_getUserBasicInfo', config('RPC_API.passport'));
    // var_dump($userInfo);exit("asds");
    // if(empty($userInfo['result']) )
    if($userInfo['result']['data']['from_user_id'] == 0 )
        exit("注册用户没有from_user_id");

    if($userInfo['result']['data']['create_time'] >= $usefulTime['start_time'] && $userInfo['result']['data']['create_time'] <= $usefulTime['end_time']){
        //发送1w体验金  type =2 体验金
        $send = new SendCouponRpcImpl();
        $sendRes = $send->activitySendAction(2, $userId, $nodeId);

    }else{
        exit("该用户的注册日期不在活动范围内");
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



