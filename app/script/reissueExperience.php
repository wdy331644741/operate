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
    		echo "------------------------模拟",$reissueDate."发放体验金（15天前所有首冲的用户）---------------------\n";
    		$date = date('Y-m-d', strtotime('-15 day',strtotime($reissueDate)));//此次循环的日期 往前推15天
    		// echo $date."\n";
		    $postParams = array(
		        'date' => $date,
		    );
		    //获取所有本月签到时间
		    $message = Common::jsonRpcApiCall((object)$postParams, 'getAllRechargeUser', config('RPC_API.passport'));
		    logs($message,"reissueExperience{$reissueDate}");
		    //var_dump($message['result']['data']);
		    $earnings = new \Model\ConfigEarnings();
		    $configEarningsData = $earnings->getInfoByTitle('revenueSharing');
		    $start_time = $configEarningsData['start_time'];  //配置中的开始时间
		    $end_time = $configEarningsData['end_time'];  //配置中结束时间
		    $today = date('Y-m-d H:i:s', time());

		    if (count($message['result']['data']) != 0) {
		        foreach ($message['result']['data'] as $key => $item) {
		        	if(!in_array($item['user_id'],array(40065,39660,39620,39967,39527,40263) )){//去除这6个人 不发放
		        		$checkUserWithdraw = [
			                'user_id' => $item['user_id'],
			                // 'date'    => date('Y-m-d', time()),
			                'date'	  => $reissueDate,//此次循环的日期
			            ];
			            echo "用户id：",$checkUserWithdraw['user_id'],"\n";
			            echo "判断从",$reissueDate," 前15天 是否有提现","\n";
			            if ($today > $start_time && $today < $end_time) {
			                Common::jsonRpcApiCall((object)$checkUserWithdraw, 'checkUserWithdraw', config('RPC_API.passport'));
			            }
		        	}
		            
		        }
		    }
    	}

    }
    
}

/**
 * 给用户计
 * @pageroute
 */
function friendsShare()
{
    //查询所有昨天的数据
    $marketingRevenueSharing = new \Model\MarketingRevenueSharing();
    $result = $marketingRevenueSharing->getYesterdayData();

    //查看配置
    $earnings = new \Model\ConfigEarnings();
    $configEarningsData = $earnings->getInfoByTitle('revenueSharing');
    $maxAmount = $configEarningsData['amount'];  //配置中的收益最大金额
    $start_time = $configEarningsData['start_time'];  //配置中的开始时间
    $end_time = $configEarningsData['end_time'];  //配置中结束时间

    foreach ($result as $item) {

        $getUsernameByUserid = [
            'user_id' => $item['from_user_id'],
        ];

        /**
         * 获取的用户名
         */
        $usernameData = Common::jsonRpcApiCall((object)$getUsernameByUserid, 'getUsernameByUserid', config('RPC_API.passport'));
        $username = $usernameData['result']['username'];

        //判断给个用户的收益是否超过100元
        $sumamount = $marketingRevenueSharing->getSumByUserId($item['from_user_id']);
        $total = $item['amount'];
        $finallyAmount = ($sumamount + $total) < $maxAmount ? $total : ($maxAmount - $sumamount);

        if ($finallyAmount > 0) {
            $cashTotal = $item['cash_total'];
            $interestCouponTotal = $item['interest_coupon_total'];
            if ($finallyAmount != $total) {
                $cashTotal = $cashTotal * ($finallyAmount / $total);
                $interestCouponTotal = $interestCouponTotal * ($finallyAmount / $total);
            }

            $getActivityUser = [
                'user_id'    => $item['user_id'],
                'start_time' => $start_time,
                'end_time'   => $end_time
            ];
            $activityUser = Common::jsonRpcApiCall((object)$getActivityUser, 'getActivityUser', config('RPC_API.passport'));

            if (isset($activityUser['result']['data']) && $activityUser['result']['data'] != false) {
                //给用户发好友收益
                $checkUserWithdraw = [
                    'activity_id'     => $item['id'],//用户id
                    'user_id'         => $item['from_user_id'],//用户id
                    'amount'          => $finallyAmount,//总利息
                    'basics_amount'   => $cashTotal,//基本利息
                    'interest_coupon' => $interestCouponTotal,//加息劵利息
                    'user_mobile'     => $username,
                    'user_name'       => $username,
                    'type_to_cn'      => "活动端加息百分之10转入",
                ];
                $result = Common::jsonRpcApiCall((object)$checkUserWithdraw, 'insertOperation', config('RPC_API.projects'));

                if (isset($result['result']) && $result['result']['code'] == 0) {
                    $marketingRevenueSharing->successExecute($item['id'], $item['from_user_id'], 200);
                } else {
                    $marketingRevenueSharing->successExecute($item['id'], $item['from_user_id'], 400);
                }
            } else {
                $marketingRevenueSharing->successExecute($item['id'], $item['from_user_id'], 400);
            }
        }
    }
}