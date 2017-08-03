<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;

/**
 * 百六加息活动
 * @pageroute
 */
function recharge(){
	$userId = I('post.user_id', '', 'intval');//用户id
	$rechargeTime = I('post.time');//充值时间
	$rechargeAmount = I('post.amount');//充值金额
	if($rechargeAmount < 10000) return "充值金额小于1w";//充值金额需大于1w

	//1 判断在此之前 充值次数
	$postParams = array(
            'userId'     => $userId,
            'startTime'  => '',//活动开始时间
            'endTime'    => $rechargeTime,
            'status'	 => 200,
        );
	$rechargeTimes = Common::jsonRpcApiCall((object)$postParams, 'getRechargeRecords', config('RPC_API.passport'));
	// $rechargeTimes = 2;
	if(count($rechargeTimes['result']) > 1) return "充值次数".count($rechargeTimes['result']);// 需要是首投

	//如果时间在活动之外
	$activity_name = 'hunderd_six'; //活动标示
	$activityModel = new \Model\MarketingActivity();
	$activityInfo = $activityModel->getUsefulActivityByName($activity_name);
	if($activityInfo['start_time'] > $rechargeTime || $activityInfo['end_time'] < $rechargeTime) return true;


	$nodeName = 'hunderd_six_keep';

	$nodeModel = new \Model\AwardNode();

    try {
    	$hunderdSixId = $nodeModel->getNode($nodeName);
        $res_one = coupon($userId,$hunderdSixId,8);
        $res_two = coupon($userId,$hunderdSixId,15);
        $res_thr = coupon($userId,$hunderdSixId,22);
        
    } catch (\Exception $e) {
        $msg = "用户ID: {$userId} 触发：{$type}，发放入账失败：" . PHP_EOL;
        $msg .= "接口错误码：{$e->getCode()}, 错误信息：{$e->getMessage()}" . PHP_EOL;
        //logs($msg, 'trigger');

        echo $msg;
    }
    return $res_one."===".$res_two."===".$res_thr;
}


/**
 * 百六加息 留存 7天  14天 21天
 * @pageroute
 */
function withdraw(){
	$userId = I('post.user_id', '', 'intval');//用户id
	$withdrawTime = I('post.datetime');//充值时间
	$withdrawAmount = I('post.amount');//充值金额
	$withdrawAmountTotal = I('post.total_amount');//累计本金
	
	//如果时间在活动之外
	$activity_name = 'hunderd_six'; //活动标示
	$activityModel = new \Model\MarketingActivity();
	$activityInfo = $activityModel->getUsefulActivityByName($activity_name);
	//在判断提现的时候，在活动结束时间的基础上再增加21天
	$realEndTime = date("Y-m-d H:i:s", strtotime("+21 days", strtotime($activityInfo['end_time'])));
	if($activityInfo['start_time'] > $withdrawTime || $realEndTime < $withdrawTime) return "提现在活动时间之外";

	$nodeName = 'hunderd_six_keep';
	$nodeModel = new \Model\AwardNode();

    $hunderdSixId = $nodeModel->getNode($nodeName);
	$awardInterestCoupon = new \Model\AwardInterestcoupon();//加息券配置
	$operateInterest = new \Model\MarketingInterestcoupon();

	$awardExpInfo = $awardInterestCoupon->filterUsefulInterestCouponNotime($hunderdSixId);
    $isExistInterest = $operateInterest->isExistArr($userId, $awardExpInfo['id']);
    if(empty($isExistInterest)) return "该用户没有百六加息券";
    $returnStr = '';
    foreach ($isExistInterest as $key => $value) {
    	# code...
    	if($value['usetime_start'] > $withdrawTime && $value['usetime_end'] > $withdrawTime){
    		//把改条加息券至为失效 发送到用户中心
    		$activePost = [
    			'token' => $value['uuid'],
    			'status'=> 0,
    		];
    		$rpcRes = Common::jsonRpcApiCall((object)$activePost, 'disableUnusedInterestCouponToUser', config('RPC_API.passport'));
			//update operate database  status
			if($rpcRes){
				$operateInterest->updateUnused($value['uuid']);
				$returnStr .= $activePost['token']."===";
			}
    	}
    }
    return $returnStr;
}

function coupon($userId,$nodeId,$later = 0){
	$awardCoupon = new \Model\AwardInterestcoupon();//加息劵配置
	$operateCoupon = new \Model\MarketingInterestcoupon();

	$awardCouponInfo = $awardCoupon->filterUsefulInterestCouponNotime($nodeId);
   	$isExistCoupon = $operateCoupon->isExistArr($userId, $awardCouponInfo['id']);
	// var_export($isExistCoupon);exit;

   	if(count($isExistCoupon) < 3){
   		//*********************发放加息劵*********************
		$couponInfo = array(
			'id' => $awardCouponInfo['id'], 
			'title' => $awardCouponInfo['title'],
			'rate' => $awardCouponInfo['rate'],
			'later_days' => $later,
			'days' => $awardCouponInfo['days'],
			'effective_days' => $awardCouponInfo['effective_days'],
			'effective_end' => $awardCouponInfo['effective_end'],
			'limit_desc' => "百六加息，留存".(int)($later-1)."天后获得",
			'is_use' => 1
			);
		$addCouponRes = $operateCoupon -> addCouponForUser($userId,$couponInfo);
		//***************************************************
		//通知用户中心发放加息劵
		unset($addCouponRes['id']);
		$proPost = [
			'interestCoupon' => $addCouponRes
		];
		$rs = Common::jsonRpcApiCall((object)$proPost, 'preSendInterestCouponToUser', config('RPC_API.passport'));

		// $activePost = [
		// 	'uuid' => $addCouponRes['uuid'],
		// 	'status' => 1,
		// ];
		// $rpcRes = Common::jsonRpcApiCall((object)$activePost, 'activateInterestCouponToUser', config('RPC_API.passport'));
		// //update operate database  status
		// if($rpcRes)
		// $operateCoupon->updateActivate($addCouponRes['uuid']);
		// logs($rpcRes,"activateInter");
		return json_encode($addCouponRes);
		
   	}

}

