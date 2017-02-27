<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");
use \App\service\rpcserverimpl\Common;
use \App\service\rpcserverimpl\OrderStatus;

/**
 * 同步订单状态
 *
 * @pageroute
 */
function run()
{
    $data = [
//        '2w148792033115230317',
//        '2w148792072477750319',
//        '2w148792100558530865',
//        '2w148792149900200369',
//        '2w148792216589940617',
//        '2w148792323498170750',
//        '2w148792438355230864',
//        '2w148792463465640595',
//        '2w148792474494480405',
//        '2w148792658173030626',
//        '2w148794474952320637',
//        '2w148794708943070215',
//        '2w148794912293900349',
//        '2w148794980270200995',
//        '2w148795099891840621',
//        '2w148795458393900720',
//        '2w148796879232030841',
//        '2w148797703907020979',
//        '2w148797992831380981',
//        '2w148798127080430410',
//        '2w148798266205800573',
//        '2w148798275355240929',
//        '2w148798302283060376',
//        '2w148798332796220913',
//        '2w148798349024990996',
//        '2w148798356637180217',
//        '2w148798413460170216',
//        '2w148798600485800794',
//        '2w148798610211140202',
//        '2w148798883530900510',
//        '2w148798889687890153',
//        '2w148798899891670382',
//        '2w148798906300220700',
//        '2w148798925032300520',
//        '2w148798938689370368',
//        '2w148798945548190185',
//        '2w148799010886250194',
//        '2w148799194119440924',
//        '2w148799655790430150',
//        '2w148799662208820421',
//        '2w148799723357200470',
//        '2w148799726140770657',
//        '2w148799747837920457',
//        '2w148799884708720539',
//        '2w148799893575550342',
//        '2w148799905361090617',
//        '2w148799928217950123',
//        '2w148799948191390968',
//        '2w148800089573530558',
//        '2w148800116548180194',
//        '2w148800119203650438',
//        '2w148800212188470165',
//        '2w148800256181280568',
//        '2w148800359362720946',
//        '2w148800504491950490',

    //失败重试1
        '2w148792072477750319',
        '2w148797703907020979',
        '2w148798127080430410',
        '2w148798413460170216',
        '2w148799194119440924',
        '2w148799928217950123',
        '2w148800119203650438',
        '2w148800212188470165',
    ];
    $success = [];
    $error = [];
    foreach ($data as $order_id) {
        try {
            $marginWithdraw = new \Model\MarginWithdraw();
            $orderInfo = $marginWithdraw->getRecordingByOrderId($order_id);

            noticeSettlement($orderInfo, '100');
            noticeSettlement($orderInfo, '200');
            fixedMargin($orderInfo['id']);

        } catch (\Exception $e) {
            $error[ $order_id ] = $e->getMessage();
            continue;
        }
        $success[ $order_id ] = 'success';

    }
    $msg = "成功信息：" . PHP_EOL;
    $msg .= var_export($success, true) . PHP_EOL;
    $msg .= "失败信息：" . PHP_EOL;
    $msg .= var_export($error, true) . PHP_EOL;

    logs($msg, 'fixed');

    echo 'complete';
}

function noticeSettlement($orderInfo, $status)
{
    //通知结算端买入成功
    $params = array(
        'user_id'     => $orderInfo['user_id'],
        'user_name'   => $orderInfo['phone'],
        'user_mobile' => $orderInfo['phone'],
        'token'       => 'f'.$orderInfo['uuid'],
        'amount'      => $orderInfo['source_amount'],
        'status'      => $status
    );

    Common::jsonRpcApiCall((object) $params, 'cashSell', config('RPC_API.projects'));
}

function fixedMargin($recordId)
{
    $marginRecord = new \Model\MarginRecord();
    $margin = new \Model\MarginMargin();
    $withdraw = new \Model\MarginWithdraw();
    $record = $marginRecord->where("`record_id` = '{$recordId}' and `type` = 'withdraw_fail'")
        ->orderby("id desc")
        ->get()->rowArr();

    $type = 'withdraw_success';
    $type_to_cn = '转出成功';
    $remark = "于 {$record['create_time']} 成功提现{$record['amount']}元.";
    $after_amount = $record['before_avaliable_amount'];
    $affect_amount = 0;
    $res = $marginRecord->where("id = {$record['id']}")->upd([
        'type'                   => $type,
        'type_to_cn'             => $type_to_cn,
        'after_avaliable_amount' => $after_amount,
        'remark'                 => $remark,
        'is_affected_amount'     => $after_amount

    ]);

    if ($res === false) {
        throw new \Exception('修改流水失败');
    }

    $res = $margin->where("user_id = {$record['user_id']}")->upd([
        'avaliable_amount' => $after_amount
    ]);

    if ($res === false) {
        throw new \Exception('修改账户余额失败');
    }

    $res = $withdraw->where("id = {$recordId}")->upd([
        'status'      => '200',
        'real_status' => '200'
    ]);

    if ($res === false) {
        throw new \Exception('修改提现状态失败');
    }

}