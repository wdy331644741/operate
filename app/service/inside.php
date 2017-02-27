<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");

/**
 * @pageroute
 */
function index()
{
    $jsonRPCServer = new Lib\jsonRPCServer();//实例化jsonServer

    //账户相关
    $jsonRPCServer->addService(loadRpcImpl('InsideRpcImpl'));

    $jsonRPCServer->processingRequests();
}
