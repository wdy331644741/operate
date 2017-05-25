<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;

/**
 * 新手活动 注册触发
 * @pageroute
 */
function register(){
	$userId = I('post.user_id', '', 'intval');
	$time = I('post.time', '');
	//如果时间在活动之外
	$activity_name = 'new_bird'; //活动标示
	$activityModel = new \Model\MarketingActivity();
	$activityInfo = $activityModel->getUsefulActivityByName($activity_name);
	if($activityInfo['start_time'] > $time || $activityInfo['end_time'] > $time) return true;
    // $type = I('post.node_name', '', 'strval');
    $type = 'register';
    $nodeModel = new \Model\AwardNode();

    $nodeId = $nodeModel->getNode($type);
	try {

        experience($userId, $nodeId);
        //coupon($userId, $nodeId);
        
    } catch (\Exception $e) {
        $msg = "用户ID: {$userId} 触发：{$type}，发放入账失败：" . PHP_EOL;
        $msg .= "接口错误码：{$e->getCode()}, 错误信息：{$e->getMessage()}" . PHP_EOL;
        logs($msg, 'trigger');

        echo $msg;
    }
}

/**
 * 新手活动 绑卡
 * @return [type] [description]
 * @pageroute
 */
function bandcard(){

}

/**
 * 新手活动 首投
 * @return [type] [description]
 * @pageroute
 */
function recharge(){

}

//发放体验金
function experience($userId,$nodeId,$amount=0){

	$awardExperience = new \Model\AwardExperience();//体验金配置
	$operateExperience = new \Model\MarketingExperience();

	$awardExpInfo = $awardExperience->filterUsefulExperience($nodeId);

    $isExistExperience = $operateExperience->isExist($userId, $awardExpInfo['id']);

    if(empty($isExistExperience)){
	   	//***************发放体验金************************
		$experienceInfo = array(
			'id' 	     => $awardExpInfo['id'],
			'title'      => $awardExpInfo['title'],
			'amount'     => $awardExpInfo['amount'],
			'days'       => $awardExpInfo['days'],//计息时长
			'hours'       => $awardExpInfo['hours'],
			'limit_desc' => $awardExpInfo['limit_desc'],
			'amount_type'=> $awardExpInfo['amount_type'],
			);
		$addExperienceRes = $operateExperience -> addExperienceForUser($userId,$experienceInfo);
		$expId = $addExperienceRes['id'];
		unset($addExperienceRes['id']);
		//通知用户中心 预发放体验金 
		if($addExperienceRes){
			$preSend = array(
				'expAward'   => $addExperienceRes,
				);
			// $resRpc = Common::jsonRpcApiCall((object)$preSend, 'preSendExperienceGoldToUser', config('RPC_API.passport'));
			$resRpc = true;
			if($resRpc){
				$activePost = array(
					'uuid' => $addExperienceRes['uuid'], 
					);
				// Common::jsonRpcApiCall((object)$activePost, 'activateExperienceGoldToUser', config('RPC_API.passport'));
				$operateExperience->updateStatusOfUse($expId);
			}
		}
		//****************************************************		
    }

}

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
		$rpcRes = Common::jsonRpcApiCall((object)$activePost, 'activateInterestCouponToUser', config('RPC_API.passport'));
		//update operate database  status
		if($rpcRes)
		$operateCoupon->updateActivate($addCouponRes['uuid']);
		// logs($rpcRes,"activateInter");
		return true;
		
   	}

}
