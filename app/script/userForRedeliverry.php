<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");

use \App\service\rpcserverimpl\Common;

/**
 * 复投活动 脚本
 * @pageroute
 */
function index(){
	checkGiveExpreience();
	checkGiveWithdraw();
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
	echo "----------------------复投活动 发放提现劵---------------------".PHP_EOL;

	if(!empty($alluser)){
		foreach ($alluser as $key => $value) {
			# code...
			echo "用户：".$value['user_id'].PHP_EOL;
			$postParams = array(
				'userId' => $value['user_id'], 
				'startTime' => $date.' 00:00:00', 
				'endTime' => $dateNow, 
				'status' => 0, //获取提现所有的状态
				);
			$withdrawTimes = Common::jsonRpcApiCall((object)$postParams, 'getWithdrawRecords', config('RPC_API.passport'));
			// $withdrawTimes = 0;
			$withdrawTimes = empty($withdrawTimes['result'])?0:count($withdrawTimes['result']);
			echo "用户15天前提现次数：".$withdrawTimes.PHP_EOL;
			if($withdrawTimes == 0){
				//1、通知用户中心  激活提现劵
				$activePost = [
					'uuid' => $value['uuid'],
					'status' => 1,
				];
				$resBack = Common::jsonRpcApiCall((object)$activePost, 'activateWithdrawCouponToUser', config('RPC_API.passport'));
				// $resBack = true;
				echo "用户中心返回激活结果：".json_encode($resBack).PHP_EOL;
				//2、update operate withdrawcoupon 
				if($resBack)
				$withdrawModel->updateStatusOfUse($value['id']);
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
	echo "----------------------复投活动 发放体验金---------------------".PHP_EOL;

	if(!empty($alluser)){
		//获取该用户 在10天内是否提现
		foreach ($alluser as $key => $value) {
			# code...
			echo "用户：".$value['user_id'].PHP_EOL;
			$postParams = array(
				'userId' => $value['user_id'], 
				'startTime' => $date.' 00:00:00', 
				'endTime' => $dateNow, 
				'status' => 0, //获取提现所有的状态
				);
			$withdrawTimes = Common::jsonRpcApiCall((object)$postParams, 'getWithdrawRecords', config('RPC_API.passport'));
			// $withdrawTimes = 0;
			$withdrawTimes = empty($withdrawTimes['result'])?0:count($withdrawTimes['result']);
			echo "用户10天前提现次数：".$withdrawTimes.PHP_EOL;
			if($withdrawTimes == 0){
				//通知用户中心 提现劵开始使用
				$activePost = [
					'uuid' => $value['uuid'],
					'status' => 1,
				];
				$resBack = Common::jsonRpcApiCall((object)$activePost, 'activateExperienceGoldToUser', config('RPC_API.passport'));
				// $resBack = true;
				echo "用户中心返回激活结果：".json_encode($resBack).PHP_EOL;
				//update operate MarketingExperience status
				if($resBack)
				$expreienceModel->updateStatusOfUse($value['id']);
			}
		
		}
	}
}