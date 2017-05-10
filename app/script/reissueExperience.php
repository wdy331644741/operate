<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");

use \App\service\rpcserverimpl\Common;

/**
 * 首投成功即可获得，坚持15天不退出自动计息，计息3天
 * 5888元体验金
 * @pageroute
 */
function index()
{   
    if(isset($GLOBALS['cli_args'][0])){
    	
    	$d1 = strtotime($GLOBALS['cli_args'][0]);
    	$d2 = strtotime($GLOBALS['cli_args'][1]);
    	$Days = round(($d2-$d1)/3600/24) + 1;//补发体验金
    	for ($i=0; $i < $Days; $i++) {
    		$reissueDate = date("Y-m-d",strtotime("+{$i} day",strtotime($GLOBALS['cli_args'][0])));
    		echo "---------------------------",$reissueDate."发放体验金（15天前所有首冲的用户）---------------------\n";
    		$date = date('Y-m-d', strtotime('-15 day',strtotime($reissueDate)));//此次循环的日期 往前推15天
    		// echo $date."\n";
		    $postParams = array(
		        'date' => $date,
		    );
		    //获取所有本月签到时间
		    $message = Common::jsonRpcApiCall((object)$postParams, 'getAllRechargeUser', config('RPC_API.passport'));
		    //logs($message,"reissueExperience{$reissueDate}");
		    // var_dump($message);
		    $earnings = new \Model\ConfigEarnings();
		    $configEarningsData = $earnings->getInfoByTitle('revenueSharing');
		    $start_time = $configEarningsData['start_time'];  //配置中的开始时间
		    $end_time = $configEarningsData['end_time'];  //配置中结束时间
		    $today = date('Y-m-d H:i:s', time());

		    if (!empty($message['result']['data']['is_frist'])) {
		        foreach ($message['result']['data']['is_frist'] as $key => $item) {
		        	// if(!in_array($item['user_id'],array(40065,39660,39620,39967,39527,40263) )){//去除这6个人 不发放
		        		$checkUserWithdraw = [
			                'user_id' => $item['user_id'],
			                // 'date'    => date('Y-m-d', time()),
			                'date'	  => $reissueDate,//此次循环的日期
			            ];
			            echo "用户id：",$checkUserWithdraw['user_id'],"\n";
			            if ($today > $start_time && $today < $end_time) {
			                $rpcRes = Common::jsonRpcApiCall((object)$checkUserWithdraw, 'checkUserWithdraw', config('RPC_API.passport'));
							if($rpcRes['result']['data']['withdraw'] == false){
								echo "从",$reissueDate," 前15天 没有提现","\n";
								if($rpcRes['result']['data']['addExp']['experience_amount'] != 0){
									echo "发放体验金：".$rpcRes['result']['data']['addExp']['experience_amount']."\n";
								}else{
									echo "已发放"."\n";
								}
							}else echo "从",$reissueDate," 前15天 有提现","\n";
			            }
		        	// }
		            
		        }
		    }
    	}

    }
    
}
