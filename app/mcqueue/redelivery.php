<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;
use App\service\exception\AllErrorException;
/**
 * 复投发放体验金/加息劵
 * 判断是否是复投，发放体验金（待激活）、加息劵
 * @pageroute
 */
function redeliveryExperience(){

	logs('记录复投发放体验金:' . PHP_EOL . var_export($_POST, true), 'redeliveryExperience');
	$userId = I('post.userId', '', 'intval');//用户id
	$rechargeTime = I('post.time');//充值时间
	$rechargeAmount = I('post.amount');//充值金额
	// $nodeName = I('post.node');//动作节点
	$nodeName = 'recharge';

	// $operateExperience = new \Model\MarketingExperience();
	// $operateCoupon = new \Model\MarketingInterestcoupon();
	$awardNode = new \Model\AwardNode();//活动节点
	// $awardExperience = new \Model\AwardExperience();//体验金配置
	// $awardCoupon = new \Model\AwardInterestcoupon();//加息劵配置

	$nodeId = $awardNode->getNode($nodeName);//获取节点id
	if(!empty($nodeId)){
		//活动节点不存在
	}

	//从用户中心 收到充值动作
	//1 判断在此之前 充值次数
	$postParams = array(
            'userId'     => $userId,
            'startTime'  => '',
            'endTime'    => $rechargeTime,
            'status'	 => 200,
        );
	//$rechargeTimes = Common::jsonRpcApiCall((object)$postParams, 'getRechargeRecords', config('RPC_API.passport'));
	$rechargeTimes = 2;
	if($rechargeTimes == 2){
		coupon($userId,$nodeId);
		experience($userId,$nodeId,$rechargeAmount);
		freeWithdraw($userId,$nodeId);
	}
}

/**
 * 复投发加息劵
 * @pageroute
 */
function coupon($userId,$nodeId){
	$awardCoupon = new \Model\AwardInterestcoupon();//加息劵配置
	$operateCoupon = new \Model\MarketingInterestcoupon();

	$awardCouponInfo = $awardCoupon->filterUsefulInterestCoupon($nodeId);
	//复投加息劵只能获得一次
   	$isExistCoupon = $operateCoupon->isExist($userId, $awardCouponInfo['id']);
   	if(empty($isExistCoupon)){
   		//*********************发放加息劵*********************
		$couponInfo = array(
			'id' => $awardCouponInfo['id'], 
			'title' => $awardCouponInfo['title'],
			'rate' => $awardCouponInfo['rate'],
			'days' => $awardCouponInfo['days'],
			'limit_desc' => $awardCouponInfo['limit_desc'],
			);
		$addCouponRes = $operateCoupon -> addCouponForUser($userId,$couponInfo);
		//***************************************************
		//通知用户中心发放加息劵
		unset($addCouponRes['id']);
		$proPost = [
			'interestCoupon' => $addCouponRes
		];
		$rs = Common::jsonRpcApiCall((object)$proPost, 'preSendInterestCouponToUser', config('RPC_API.passport'));

		$activePost = [
			'uuid' => $addCouponRes['uuid'],
			'status' => 1,
		];
		// $rpcRes = Common::jsonRpcApiCall((object)$activePost, 'activateInterestCouponToUser', config('RPC_API.passport'));
		//update operate database  status
		$operateCoupon->updateActivate($addCouponRes['uuid']);
		logs($rpcRes,"activateInter");
		return true;
		
   	}

}

/**
 * 复投发体验金
 * @pageroute
 */
function experience($userId,$nodeId,$amount){

	$awardExperience = new \Model\AwardExperience();//体验金配置
	$operateExperience = new \Model\MarketingExperience();

	$awardExpInfo = $awardExperience->filterUsefulExperience($nodeId);
	//复投体验金只能获得一次
    $isExistExperience = $operateExperience->isExist($userId, $awardExpInfo['id']);
    if(empty($isExistExperience)){
	   	//***************发放体验金************************
		$experienceInfo = array(
			'id' 	     => $awardExpInfo['id'],
			'title'      => $awardExpInfo['title'],
			'amount'     => $amount,
			'days'       => $awardExpInfo['days'],//10天后有效 +5天使用时间
			'limit_desc' => $awardExpInfo['limit_desc'],
			'amount_type'=> $awardExpInfo['amount_type'],
			);
		$addExperienceRes = $operateExperience -> addExperienceForUser($userId,$experienceInfo,10);
		unset($addExperienceRes['id']);
		//通知用户中心 预发放体验金 
		if($addExperienceRes){
			$activePost = array(
				'expAward'   => $addExperienceRes,
				);
			$resRpc = Common::jsonRpcApiCall((object)$activePost, 'preSendExperienceGoldToUser', config('RPC_API.passport'));
		}
		//****************************************************		
    }

}

/**
 * 复投发提现劵
 * @pageroute
 */
function freeWithdraw($userId,$nodeId){
	$awardWithdraw = new \Model\AwardWithdraw();
	$awardWithdrawInfo = $awardWithdraw->filterUsefulWithdraw($nodeId);
	
	$FreeWithdraw = new \Model\MarketingWithdrawcoupon();

	$isExistCoupon = $FreeWithdraw->isExist($userId, $awardWithdrawInfo['id']);

	if(empty($isExistCoupon)){
		$withdrawInfo = array(
			'id' => $awardWithdrawInfo['id'], 
			'title' => $awardWithdrawInfo['title'], 
			// 'remain_times' => $awardWithdrawInfo['times'], 
			'limit_desc' => $awardWithdrawInfo['limit_desc'], 
			);
		$addWithdrawRes = $FreeWithdraw -> addWithdrawForUser($userId,$withdrawInfo);
		unset($addWithdrawRes['id']);
		//通知用户中心 发放提现劵
		if($addWithdrawRes){
			$activePost = array(
				'withdrawCoupon'   => $addWithdrawRes,
				);
			Common::jsonRpcApiCall((object)$activePost, 'preSendWithdrawCouponToUser', config('RPC_API.passport'));
		}
	}
}