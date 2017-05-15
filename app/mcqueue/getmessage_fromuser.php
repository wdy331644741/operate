<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;
use App\service\exception\AllErrorException;
/**
 * 从用户中心 拿到充值行为
 * @pageroute
 */
function syncRechargeToPromoter(){
	logs('充值:' . PHP_EOL . var_export($_POST, true), 'syncPromoterRecharge');
	$userId = I('post.userId', '', 'intval');//用户id
	$rechargeTime = I('post.time');//充值时间
	$rechargeAmount = I('post.amount');//充值金额

	//从用户中心获取基本信息
	$postParams = [
		'userId'     => $userId
	];
	$rechargeUserInfo = Common::jsonRpcApiCall((object)$postParams, 'getUserBaseInfo', config('RPC_API.passport'));
	//得到邀请关系
	$fromUser = $rechargeUserInfo['result']['from_user_id'];
	// var_export($rechargeUserInfo['result']['from_user_id']);exit;
	$promoterModel = new \Model\PromoterList();
	$havePromoter = $promoterModel-> getPromoterInfoById($fromUser);
	var_export($havePromoter);exit;

}