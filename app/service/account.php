<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");

/**
 * @pageroute
 */
function index()
{
    $jsonRPCServer = new Lib\jsonRPCServer();//实例化jsonServer

    //账户相关
    $jsonRPCServer->addService(loadRpcImpl('AccountRpcImpl'));

    //活动相关
    $jsonRPCServer->addService(loadRpcImpl('ActivityRpcImpl'));

    //用戶賬目
    $jsonRPCServer->addService(loadRpcImpl('UserAccountRpcImpl'));


    //兑换码
    $jsonRPCServer->addService(loadRpcImpl('RedeemRpcImpl'));
    $jsonRPCServer->processingRequests();
}

/**
 * 输出图片验证码
 *
 * @pageroute
 */
function captcha()
{
    $sid = I('get.sid', '', 'htmlspecialchars');
    //sessionHandle
    $sessionHandle = new \Lib\Session($sid);
    $config = empty(C('CAPTCHA_CONF')) ? array() : C('CAPTCHA_CONF');

    $captcha = new \Lib\Captcha\Captcha($sessionHandle, $config);
    $captcha->entry();
}