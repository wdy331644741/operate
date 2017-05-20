<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");

use \App\service\rpcserverimpl\Common;

/**
 * 复投活动 脚本
 * @pageroute
 */
function index(){
	checkGiveWithdraw();
	checkGiveExpreience();
}

/**
 * 用户复投后，即可获得一张免费提现券，该券永久有效，用户完成二次投资开始
 * 15天内不提现即可获得。
 * @pageroute
 */
function checkGiveWithdraw(){
	$withdrawModel = new \Model\MarketingWithdrawcoupon();
	//检测15天前 是否有提现 行为
	$date = date('Y-m-d',strtotime('-15 day'));
	$dateNow = date('Y-m-d H:i:s');
	$alluser = $withdrawModel->getCouponUserByTime($date);

	if(!empty($alluser)){
		foreach ($alluser as $key => $value) {
			# code...
			$postParams = array(
				'userId' => $value['user_id'], 
				'startTime' => $date.' 00:00:00', 
				'endTime' => $dateNow, 
				'status' => 200, 
				);
			// $withdrawTimes = Common::jsonRpcApiCall((object)$postParams, 'getWithdrawRecords', config('RPC_API.passport'));
			$withdrawTimes = 0;
			if($withdrawTimes == 0){
				//1、update operate withdrawcoupon 
				$withdrawModel->updateStatusOfUse($value['id']);
				//2、通知用户中心  激活提现劵
				// $activePost = [
				// 	'uuid' => $addCouponRes['uuid'],
				// 	'status' => 1,
				// ];
				//Common::jsonRpcApiCall((object)$postParams, 'getWithdrawRecords', config('RPC_API.passport'));
			}
		}
	}

}


/**
 * 用户复投后，即可获得等于复投金额的体验金，
 * 体验金使用期限5天，用户完成二次投资开始计算
 * 10天内不提现自动计息。
 * @pageroute
 */
function checkGiveExpreience(){
	$expreienceModel = new \Model\MarketingExperience();
	//检测10天前 复投的用户是否有提现行为
	$date = date('Y-m-d',strtotime('-10 day'));
	$dateNow = date('Y-m-d H:i:s');
	$alluser = $expreienceModel->getExpUserByTime($date);
	// var_export($alluser);exit;

	if(!empty($alluser)){
		//获取该用户 在10天内是否提现
		foreach ($alluser as $key => $value) {
			# code...
			$postParams = array(
				'userId' => $value['user_id'], 
				'startTime' => $date.' 00:00:00', 
				'endTime' => $dateNow, 
				'status' => 200, 
				);
			// $withdrawTimes = Common::jsonRpcApiCall((object)$postParams, 'getWithdrawRecords', config('RPC_API.passport'));
			$withdrawTimes = 0;
			if($withdrawTimes == 0){
				//update operate MarketingExperience status
				$expreienceModel->updateStatusOfUse($value['id']);
				//通知用户中心 提现劵开始使用
				// Common::jsonRpcApiCall((object)$postParams, 'getWithdrawRecords', config('RPC_API.passport'));
			}
		
		}
	}
}