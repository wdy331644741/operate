<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;

/**
 * 注册成功
 *
 * @pageroute
 **/
function signup()
{
    //日志记录
    logs('注册消息请求:' . PHP_EOL . var_export($_POST, true), 'mcqueue_debug');

    $userId = I('post.user_id', '', 'intval');
    // $inviteCode = I('post.invite_code', '', 'strval');

    //站内信通知
    Common::sendWebMessage($userId, 'signup');
}

/**
 * 提现流程
 *
 * @pageroute
 **/
function withdraw()
{
    //日志记录
    logs('提现消息请求:' . PHP_EOL . var_export($_POST, true), 'mcqueue_debug');

    $id = I('post.withdraw_id', '', 'intval');
    $type = I('post.type', '', 'strval');
    $userId = I('post.user_id', '', 'intval');
    $datetime = I('post.datetime', date("m月d日H时i分"));

    //实例模型
    $authUser = new \Model\AuthUser($userId);
    $withdrawModel = new \Model\MarginWithdraw();
    $couterModel = new \Model\MarginRefundCounter();
    //提现记录信息
    $withdrawInfo = $withdrawModel->where(array('id' => $id))->get()->rowArr();

    /**
     * 组织银行名称
     */
    $lastCardNo = substr($withdrawInfo['bank_account'], -4);
    $bankName = $withdrawInfo['bank_name'] . "({$lastCardNo})";

    /**
     * 提现金额
     */
    $amount = $type == 'withdraw_success' ? $withdrawInfo['amount'] : $withdrawInfo['source_amount'];

    //提交审核，查询代付结果
    if ($type == 'withdraw_audit') {
        Common::localApiCall((object) ['order_id' => $withdrawInfo['order_id']], 'inquireWithdraw', 'FundsRpcImpl');
    }

    //"前两次"提现，失败减次数
    if ($type == 'withdraw_fail' && $withdrawInfo['fee'] == 0) {
        $couterModel->decrementCounter($withdrawInfo['user_id'], $withdrawInfo['create_time']);
    }

    //提现失败，检查是否退还手续费
    if (
        ($type == 'withdraw_fail' && $withdrawInfo['fee'] == 0) ||
        ($type == 'withdraw_success' && $withdrawInfo['fee'] > 0)
    ) {
        //退还手续费、加次数(refundFee有次数检查)
        $withdrawModel->refundFee($withdrawInfo['user_id'], $withdrawInfo['create_time']);
    }

    if ($type == 'withdraw_audit' || $type == 'withdraw_success' || $type == 'withdraw_fail') {
        //发送短信
        Common::sendMessage($authUser->phone, $type, array(
            'name'     => $authUser->getUserName($userId, true),
            'datetime' => $datetime,
            'amount'   => $amount,
            'bankname' => $bankName
        ));
    }
}

/**
 * 充值通知地址
 *
 * @pageroute
 **/
function recharge()
{
    //记录日志
    logs('充值消息请求:' . PHP_EOL . var_export($_POST, true), 'mcqueue_debug');

    $userId = I('post.user_id', 0, 'intval');
    $datetime = I('post.datetime', date("m月d日 H时i分"));
    $amount = I('post.amount', '');

    $authUser = new \Model\AuthUser($userId);

    if (empty($userId) || empty($datetime) || empty($amount)) {
        return false;
    }

    //发送短信
    Common::sendMessage($authUser->phone, 'recharge', array(
        'name'     => $authUser->getUserName($userId, true),
        'datetime' => $datetime,
        'amount'   => $amount
    ));
}

/**
 * 修改绑定手机通知地址
 *
 * @pageroute
 **/
function bindphone()
{
    //记录日志
    logs('修改绑定手机消息请求:' . PHP_EOL . var_export($_POST, true), 'mcqueue_debug');

    $userId = I('post.user_id', '', 'intval');
    //实例模型
    $authUser = new \Model\AuthUser($userId);
    $userName = $authUser->getUserName($userId);
    if ($userName === false) {
        return false;
    }

    //短信通知
    Common::sendMessage($authUser->phone, 'change_phone_success', array(
        'name' => $userName
    ));
}

