<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;
use App\service\exception\AllErrorException;
/**
 * 阶梯加息
 * @pageroute
 */
function ladderInterestcoupon(){

	// logs('记录复投发放体验金:' . PHP_EOL . var_export($_POST, true), 'redeliveryExperience');
	$userId = I('post.userId', '', 'intval');//用户id
	$rechargeTime = I('post.time');//充值时间
	$rechargeAmount = I('post.amount');//充值金额
	$rechargeAmountTotal = I('post.amount');//累计本金
	// $nodeName = I('post.node');//动作节点
	$nodeName = 'ladder_percent_one';

	$awardNode = new \Model\AwardNode();//活动节点
	$nodeId = $awardNode->getNode($nodeName);//获取节点id
	if(!empty($nodeId)){
		//活动节点不存在
	}
	//单笔充值大于1w  或者 累计本金大于2w
	//发放1%加息
	if($rechargeAmount >= 10000 || $rechargeAmountTotal >= 20000 ){
		coupon($userId,$nodeId);
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
		// var_dump($couponInfo);exit;
		$addCouponRes = $operateCoupon -> addCouponForUser($userId,$couponInfo);
		//***************************************************
		//通知用户中心发放加息劵
		unset($addCouponRes['id']);
		$proPost = [
			'interestCoupon' => $addCouponRes
		];
		echo json_encode($proPost);exit;
		$rs = Common::jsonRpcApiCall((object)$proPost, 'preSendInterestCouponToUser', config('RPC_API.passport'));

		$activePost = [
			'uuid' => $addCouponRes['uuid'],
			'status' => 1,
		];
		$rpcRes = Common::jsonRpcApiCall((object)$activePost, 'activateInterestCouponToUser', config('RPC_API.passport'));
		//update operate database  status
		logs($rpcRes,"ladder_percent_one");
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
			'days'       => $awardExpInfo['days'],
			'limit_desc' => $awardExpInfo['limit_desc'],
			'amount_type'=> $awardExpInfo['amount_type'],
			);
		$addExperienceRes = $operateExperience -> addExperienceForUser($userId,$experienceInfo);
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