<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");

use \App\service\rpcserverimpl\Common;

/**
 * 复投活动 脚本
 * @pageroute
 */
function index(){
	if(!isset($GLOBALS['cli_args'][0])) return "undefind params!";
	$d1 = $GLOBALS['cli_args'][0];
    //$d2 = strtotime($GLOBALS['cli_args'][1]);
	checkGiveExpreience($d1);
	//checkGiveWithdraw();
}

/**
 * 求两个日期之间相差的天数
 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
 * @param string $day1
 * @param string $day2
 * @return number
 */
function diffBetweenTwoDays($day1, $day2)
{
  $second1 = strtotime($day1);
  $second2 = strtotime($day2);
    
  if ($second1 < $second2) {
    $tmp = $second2;
    $second2 = $second1;
    $second1 = $tmp;
  }
  return ($second1 - $second2) / 86400;
}

/**
 * 用户复投后，即可获得等于复投金额的体验金，
 * 体验金使用期限5天，用户完成二次投资开始计算
 * 10天内不提现自动计息。
 * $d1 = 2017-06-18;
 * @pageroute
 */
function checkGiveExpreience($d1){
	$expreienceModel = new \Model\MarketingExperience();

	$dateNow = date('Y-m-d');//脚本是 0点执行
	$day_diff = diffBetweenTwoDays($d1,$dateNow);
	$debug_day = $day_diff+10;
	//检测10天前 复投的用户是否有提现行为
	$date = date('Y-m-d',strtotime("-{$debug_day} day" , time()) );

	$alluser = $expreienceModel->getExpUserByTime($date);
	echo "----------------------复投活动 发放体验金---------------------".PHP_EOL;

	if(!empty($alluser)){
		//获取该用户 在10天内是否提现
		foreach ($alluser as $key => $value) {
			# code...
			echo "用户：".$value['user_id'].PHP_EOL;
			$postParams = array(
				'userId' => $value['user_id'], 
				'startTime' => $value['create_time'],
				'endTime' => $d1, 
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
				if(isset($resBack['result']) && $resBack['result'] == true)
				$expreienceModel->updateStatusOfUse($value['id']);
			}
		
		}
	}
}

