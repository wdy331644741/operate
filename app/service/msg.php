<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");

/**
 * @pageroute
 */
function index()
{
    $jsonRPCServer = new Lib\jsonRPCServer();//实例化jsonServer

    //推送相关
    $jsonRPCServer->addService(loadRpcImpl('PushRpcImpl'));

    $jsonRPCServer->processingRequests();
}
