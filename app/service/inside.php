<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");

/**
 * @pageroute
 */
function index()
{
    $jsonRPCServer = new Lib\jsonRPCServer();//实例化jsonServer

    //对内接口
    $jsonRPCServer->addService(loadRpcImpl('ConfigRpcImpl'));

    $jsonRPCServer->processingRequests();
}
