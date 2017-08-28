<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;
use App\service\exception\AllErrorException;
/**
 * 阶梯加息 - 充值
 * @pageroute
 */
function ladderInterestcoupon(){

    // logs('记录复投发放体验金:' . PHP_EOL . var_export($_POST, true), 'redeliveryExperience');
    $userId = I('post.user_id', '', 'intval');//用户id
    $rechargeTime = I('post.time');//充值时间
    $rechargeAmount = I('post.amount');//充值金额
    $rechargeAmountTotal = I('post.total');//累计本金
    // $nodeName = I('post.node');//动作节点
    $percentOne = 'ladder_percent_one';
    $percentHalfKeep = 'ladder_percent_half_keep';

    $awardNode = new \Model\AwardNode();//活动节点
    $nodeId = $awardNode->getNode($percentOne);//获取节点id
    if(!empty($nodeId)){
        //活动节点不存在
    }
    //单笔充值大于1w  或者 累计本金大于2w
    //单笔充值 小于1w 发放一张0.5阶梯加息劵 7天
    //发放1%加息 结束时间=阶梯加息活动结束时间

    if($rechargeAmountTotal >= 20000 || $rechargeAmount >= 10000){
        $nodeId = $awardNode->getNode($percentOne);
        if(empty($nodeId))
            throw new AllErrorException(AllErrorException::ACTIVATE_NODE, [], '获取活动节点失败');
        coupon($rechargeTime,$userId,$nodeId);//ladder_percent_one_keep 1%的 发放并激活

        // }else if($rechargeAmount >= 10000 && $rechargeAmountTotal < 20000){
        // 	coupon($userId, $awardNode->getNode($percentOne) ); //ladder_percent_one_keep 1%的 发放并激活
    }else if($rechargeAmount < 10000){
        //判断是否已经发放加息劵
        $operateCoupon = new \Model\MarketingInterestcoupon();
        $alreadyGave = $operateCoupon->getActivateAndStatusData($userId);
        if($alreadyGave) return true;
        $half = $awardNode->getNode($percentHalfKeep);
        $one = $awardNode->getNode($percentOne);
        if(empty($half) || empty($one))
            throw new AllErrorException(AllErrorException::ACTIVATE_NODE, [], '获取活动节点失败');
        coupon($rechargeTime,$userId, $half, true,7); //发一个7天 0.5的 发放并激活
        coupon($rechargeTime,$userId, $one,true,14); //预发 一个1%的 发放
    }
}

/**
 * 阶梯加息 - 提现
 * @pageroute
 */
function disLadderInterestcoupon(){
    $userId = I('post.user_id', '', 'intval');//用户id
    $withdrawTime = I('post.datetime');//充值时间
    $withdrawAmount = I('post.amount');//充值金额
    $withdrawAmountTotal = I('post.total_amount');//累计本金

    if($withdrawAmountTotal >= 20000) return true;
    $ladderPercentOne = 'ladder_percent_one';
    $percentHalfKeep = 'ladder_percent_half_keep';
    $awardNode = new \Model\AwardNode();//活动节点
    // $nodeId = $awardNode->getNode($ladderPercentOne);//获取节点id
    $nodeId = array(
        $awardNode->getNode($ladderPercentOne),
        $awardNode->getNode($percentHalfKeep)
    );
    if(empty($nodeId)){
        throw new AllErrorException(AllErrorException::ACTIVATE_NODE, [], '获取活动节点失败');
    }

    //查询operate_加息劵表中是否给该用户激活过加息劵
    $awardCoupon = new \Model\AwardInterestcoupon();//加息劵配置
    $operateCoupon = new \Model\MarketingInterestcoupon();
    $awardCouponInfo = $awardCoupon->filterUsefulInterestCoupon($nodeId);
    $awardCouponIds = array_column($awardCouponInfo,'id');
    $isExistCoupon = $operateCoupon->isActivateExist($userId, $awardCouponIds);

    if(empty($isExistCoupon)) return true;
    if(count($isExistCoupon)>1){
        // var_export($isExistCoupon);exit;

        if($isExistCoupon[1]['effective_start'] < $withdrawTime && $isExistCoupon[1]['effective_end'] > $withdrawTime ){
            //提现时间在0.5加息时间段内
            //更新0.5结束时间、并取消1%
            informdisable($isExistCoupon[1]['uuid'],1,0,$isExistCoupon[1]['effective_start'],$withdrawTime);
            informdisable($isExistCoupon[0]['uuid'],0,0,$isExistCoupon[0]['effective_start'],$withdrawTime);
            // $operateCoupon->updateActivate($isExistCoupon[1]['uuid'],1,0,$isExistCoupon[1]['effective_start'],$withdrawTime);
            // $operateCoupon->updateActivate($isExistCoupon[0]['uuid'],0,0);
            coupon($withdrawTime,$userId, 21, true,7); //发一个7天 0.5的 发放并激活
            coupon($withdrawTime,$userId, 20,true,14); //预发 一个1%的 发放
            echo "提现时间在0.5加息时间段内";exit;
        }else if($isExistCoupon[0]['effective_start'] < $withdrawTime && $isExistCoupon[0]['effective_end'] > $withdrawTime){
            //提现时间在1%加息时间段内
            //更新1% 结束时间
            informdisable($isExistCoupon[0]['uuid'],1,0,$isExistCoupon[0]['effective_start'],$withdrawTime);
            informdisable($isExistCoupon[1]['uuid'],0,0,$isExistCoupon[1]['effective_start'],$withdrawTime);
            coupon($withdrawTime,$userId, 21, true,7); //发一个7天 0.5的 发放并激活
            coupon($withdrawTime,$userId, 20,true,14); //预发 一个1%的 发放
            // $operateCoupon->updateActivate($isExistCoupon[0]['uuid'],1,0,$isExistCoupon[0]['effective_start'],$withdrawTime);
            // $operateCoupon->updateActivate($isExistCoupon[1]['uuid'],1,0);
            echo "提现时间在1%加息时间段内";exit;
        }else if($isExistCoupon[1]['effective_start'] > $withdrawTime){
            //提现时间在加息之前
            //把0.5  1的加息券全部都干掉
            informdisable($isExistCoupon[1]['uuid'],0,0,$isExistCoupon[1]['effective_start'],$withdrawTime);
            informdisable($isExistCoupon[0]['uuid'],0,0,$isExistCoupon[0]['effective_start'],$withdrawTime);
            coupon($withdrawTime,$userId, 21, true,7); //发一个7天 0.5的 发放并激活
            coupon($withdrawTime,$userId, 20,true,14); //预发 一个1%的 发放
            // $operateCoupon->updateActivate($isExistCoupon[1]['uuid'],0,0);
            // $operateCoupon->updateActivate($isExistCoupon[0]['uuid'],0,0);
            echo "提现时间在加息之前";exit;
        }
    }elseif(count($isExistCoupon) == 1 && $isExistCoupon[0]['effective_start'] < $withdrawTime){
        //如果只有一张1%的
        //停止计息  调取用户中心 接口
        $disactivePost = [
            // 'uuid' => $addCouponRes['uuid'],
            // 'status' => 1,
            'token' => $isExistCoupon[0]['uuid'],
            'status' => 0,
            // 'interestcouponId' => $coupon['id'],
            'loseTime'  => $withdrawTime,
        ];
        $rs = Common::jsonRpcApiCall((object)$disactivePost, 'disableInterestCouponToUser', config('RPC_API.passport'));
        // $rs = true;
        if($rs){
            $operateCoupon->updateActivate($isExistCoupon[0]['uuid'],0,0);
            coupon($withdrawTime,$userId, 21, true,7); //发一个7天 0.5的 发放并激活
            coupon($withdrawTime,$userId, 20,true,14); //预发 一个1%的 发放
        }
    }
}

/**
 * 阶梯发加息劵
 */
function coupon($rechargeTime,$userId,$nodeId,$activate=true,$laterDays=0){
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
        $top = $operateCoupon->isExist($userId,10);//存在1%的阶梯加息
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
        if(count($res) > 1 && $res[$isExistCoupon['id']]['source_id'] == 10){
            //2、把这两张券直接只为失效
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
 * 提现禁用加息券
 */
function informdisable($uuid,$activate=1,$status=1,$effective_start='',$effective_end=''){
    $operateCoupon = new \Model\MarketingInterestcoupon();
    $disactivePost = [
        // 'uuid' => $addCouponRes['uuid'],
        // 'status' => 1,
        'token' => $uuid,
        'status' => $status,
        // 'interestcouponId' => $coupon['id'],
        'loseTime'  => $effective_end,
    ];
    $rs = Common::jsonRpcApiCall((object)$disactivePost, 'disableInterestCouponToUser', config('RPC_API.passport'));
    if($rs['result']){
        $operateCoupon->updateActivate($uuid,1,0,$effective_start,$effective_end);
        return true;
    }else{
        return false;
    }
}



