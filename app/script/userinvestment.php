<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * 发放脚本
 * @pageroute
 */
use \App\service\rpcserverimpl\Common;

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
