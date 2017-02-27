<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");
use \App\service\rpcserverimpl\Common;
use \App\service\rpcserverimpl\OrderStatus;

defined("RECHARGE") || define("RECHARGE", 'recharge');
defined("WITHDRAW") || define("WITHDRAW", 'withdraw');

/**
 * 同步订单状态
 *
 * @pageroute
 */
function run()
{
    $type = isset($GLOBALS['cli_args'][0]) ? $GLOBALS['cli_args'][0] : RECHARGE;
    if (!in_array($type, [RECHARGE, WITHDRAW])) {
        throw new Exception("参数不合法！");
    }
    $datetime = date("Y-m-d 00:00:00");

    if($type == RECHARGE) {
        syncRechargeOrder($datetime);
    }

    if($type == WITHDRAW) {
        syncWithdrawOrder($datetime);
    }
}

function syncRechargeOrder($datetime)
{
    $rechargeModel = new \Model\MarginRecharge();
    $processingRecords = $rechargeModel
        ->fields("id, order_id, user_id, realname")
        ->where("`create_time` >= '{$datetime}' and `status` = 100")
        ->get()->resultArr();

    foreach ($processingRecords as $record) {
        //唯一查询编号
        $request_id = generate_orderid();

        //查询订单状态(APP)
        $orderStatus = Common::jsonRpcApiCall(
            [$record['order_id'], $request_id], 'orderQuery', config('RPC_API.pay'), false
        );
        try {
            //处理银行接口数据，并返回状态信息
            $result = OrderStatus::getRechargeStatus($orderStatus, $record['user_id'], $record['order_id']);
        } catch (\Exception $e) {
            $result = array(
                'error_code' => $e->getCode(),
                'error_msg'  => $e->getMessage()
            );
        }

        logs($result, 'rechargeSyncCrontab');
    }

    return true;
}

function syncWithdrawOrder($datetime)
{
    $withdrawModel = new \Model\MarginWithdraw();
    $processingRecords = $withdrawModel
        ->fields("id, order_id, user_id, realname")
        ->where("`create_time` >= '{$datetime}' and `status` = 110")
        ->get()->resultArr();
    foreach ($processingRecords as $record) {

        $response = Common::jsonRpcApiCall(
            [$record['order_id']], 'withdrawQuery', config('RPC_API.pay'), false
        );

        if (isset($response['result']['status'])) {
            if (
                $response['result']['status'] == 1 ||
                $response['result']['status'] == 0
            ) {
                $result = $withdrawModel->modifyWithdrawStatus($record['order_id'], $response['result']['status']);

                logs([
                    'order_id' => $record['order_id'],
                    'result'   => $result
                ], 'withdrawSyncCrontab');
            }
        }
    }
}