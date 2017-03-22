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
    $date = date('Y-m-d', strtotime('-15 day'));
    $postParams = array(
        'date' => $date,
    );
    //获取所有本月签到时间
    $message = Common::jsonRpcApiCall((object)$postParams, 'getAllRechargeUser', config('RPC_API.passport'));

    if (count($message['result']['data']) != 0) {
        foreach ($message['result']['data'] as $key => $item) {
            $checkUserWithdraw = [
                'user_id' => $item['user_id'],
                'date'    => date('Y-m-d', time()),
            ];

            Common::jsonRpcApiCall((object)$checkUserWithdraw, 'checkUserWithdraw', config('RPC_API.passport'));
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
        if ($sumamount < $maxAmount || ($sumamount + $item['amount']) < $maxAmount) {
            //给用户发好友收益
            $checkUserWithdraw = [
                'activity_id'     => $item['id'],//用户id
                'user_id'         => $item['from_user_id'],//用户id
                'amount'          => $item['amount'],//总利息
                'basics_amount'   => $item['cash_total'],//基本利息
                'interest_coupon' => $item['interest_coupon_total'],//加息劵利息
                'user_mobile'     => $username,
                'user_name'       => $username,
                'type_to_cn'      => "活动端加息百分之10转入",
            ];
            $result = Common::jsonRpcApiCall((object)$checkUserWithdraw, 'insertOperation', config('RPC_API.projects'));

            if (isset($result['data']['result']) && $result['data']['result']['code'] == 0) {
                $marketingRevenueSharing->successExecute($item['id'], $item['from_user_id'], 200);
            } else {
                $marketingRevenueSharing->successExecute($item['id'], $item['from_user_id'], 400);
            }
        }

    }

}