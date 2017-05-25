<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;

/**
 * 新手活动触发
 * @pageroute
 */
function register(){
	$userId = I('post.user_id', '', 'intval');
	$time = I('post.time', '');
    // $type = I('post.node_name', '', 'strval');
    $type = 'register';
    $nodeModel = new \Model\AwardNode();

    $nodeId = $nodeModel->getNode($type);
	try {

        experience($userId, $nodeId);
        coupon($userId, $nodeId);
        
    } catch (\Exception $e) {
        $msg = "用户ID: {$userId} 触发：{$type}，发放入账失败：" . PHP_EOL;
        $msg .= "接口错误码：{$e->getCode()}, 错误信息：{$e->getMessage()}" . PHP_EOL;
        logs($msg, 'trigger');

        echo $msg;
    }
}


//复用 复投发放体验金
function experience($userId,$nodeId,$amount=0){

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
