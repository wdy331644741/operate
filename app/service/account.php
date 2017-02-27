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

    //注册登录服务接口
    $jsonRPCServer->addService(loadRpcImpl('AuthorizeRpcImpl'));

    //通用的接口类（类似图片验证码、短信验证码）
    $jsonRPCServer->addService(loadRpcImpl('ToolsRpcImpl'));

    //账号安全相关服务
    $jsonRPCServer->addService(loadRpcImpl('SecureRpcImpl'));

    //银行卡行管服务接口
    $jsonRPCServer->addService(loadRpcImpl('BankcardRpcImpl'));

    //理财券相关服务接口
    $jsonRPCServer->addService(loadRpcImpl('CouponRpcImpl'));

    //用户资产相关服务接口
    $jsonRPCServer->addService(loadRpcImpl('FundsRpcImpl'));

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