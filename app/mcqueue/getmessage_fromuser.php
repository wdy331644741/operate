<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;
use App\service\exception\AllErrorException;
/**
 * 从用户中心 拿到充值行为
 * @pageroute
 */
function syncRechargeToPromoter(){
	logs('充值:' . PHP_EOL . var_export($_POST, true), 'syncPromoterRecharge');
	$userId = I('post.user_id', '', 'intval');//用户id
	$rechargeTime = I('post.time');//充值时间
	$rechargeAmount = I('post.amount');//充值金额
	$userPhone = I('post.phone');//手机

	$promoterModel = new \Model\PromoterList();
	//从用户中心获取基本信息
	$postParams = [
		'userId'     => $userId
	];
	$rechargeUserInfo = Common::jsonRpcApiCall((object)$postParams, 'getUserBaseInfo', config('RPC_API.passport'));
	//得到邀请关系
	$fromUser = $rechargeUserInfo['result']['from_user_id'];
	logs($rechargeUserInfo);
	$havePromoter = $promoterModel-> getPromoterInfoById($fromUser);
	// var_dump($fromUser);exit;
	// 更新邀请好友的投资金额
	if($havePromoter){
		$promoterModel->addPromoterFriendRecharge($fromUser,$rechargeAmount);
	}

}

/**
 * 从用户中心 拿到注册行为
 * @pageroute
 */
function syncInviteToPromoter(){
    logs('注册' . PHP_EOL . var_export($_POST,true),'syncPromoterInvite');
    $userId = I('post.user_id', '', 'intval');//用户id
    $from_user_id = I('post.from_user_id');
    $from_channel = I('post.from_channel');//渠道
    $promoterModel = new \Model\PromoterList();
    $havePromoter = $promoterModel-> getPromoterInfoById($from_user_id);
    //更新推广员好友数量
    if($havePromoter){
            $promoterModel->upPromoterFriendCounts($from_user_id);
    }

}

/**
 * 从用户中心 拿到提现行为
 * @pageroute
 */
function syncWithdrawToPromoter(){
	logs('提现' . PHP_EOL . var_export($_POST,true),'syncPromoterWithdraw');
}



/**
 * 用户中心 推送綁卡行为
 * @pageroute
 */
function syncBoundCard(){
	logs('綁卡' . PHP_EOL . var_export($_POST,true),'syncBoundCard');
}