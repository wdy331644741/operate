<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 15:20
 */
/**
 * 渠道列表
 * @pageroute
 */
function index()
{
    $framework =getFrameworkInstance();
    $page = I('get.p/d', 1);
    $data = [$page,100];
    $jsonRPCClient = new Lib\JsonRpcClient(CHANNELSERVICEURL);
    $result = call_user_func_array(array($jsonRPCClient,'lists'),$data);
    $num = count($result['list']);
    $framework->smarty->assign('total', $num);
    $framework->smarty->assign('lists', $result['list']);
    $framework->smarty->display('bank/list.html');
}
/**
 * 增加渠道
 * @pageroute
 */
function add()
{
    $framework = getFrameworkInstance();
    if(IS_AJAX)
    {
        $channelInfo = I('post.');
        if(!$channelInfo)
            ajaxReturn(array('error'=>'100','msg'=>'提交的信息不全'));

        $info = [$channelInfo['channel_name'],$channelInfo['channel_code'],$channelInfo['channel_status']];
        $jsonRPCClient = new Lib\JsonRpcClient(CHANNELSERVICEURL);
        $result = call_user_func_array(array($jsonRPCClient,'create'),$info);
        if(!(array_key_exists('error_code',$result)))
            ajaxReturn(array('error'=>'200','msg'=>'添加成功'));
        else
            ajaxReturn(array('error'=>'100','msg'=>'添加失败'));
    }else
    {
        $framework->smarty->display('bank/channel_add.html');
    }
}
/**
 * 编辑渠道
 * @pageroute
 */
function edit()
{
    $framework = getFrameworkInstance();
    if($_POST)
    {
        $channelInfo = I('post.');
        $info = [$channelInfo['id'],$channelInfo['channel_name'],$channelInfo['channel_code'],$channelInfo['channel_status']];
        $jsonRPCClient = new Lib\JsonRpcClient(CHANNELSERVICEURL);
        $result = call_user_func_array(array($jsonRPCClient,'modify'),$info);
        if(!(array_key_exists('error_code',$result)))
            ajaxReturn(array('error'=>'200','msg'=>'修改成功'));
        else
            ajaxReturn(array('error'=>'100','msg'=>'修改失败'));
    }else
    {
        $getInfo = I('get.');
        $framework->smarty->assign('data',$getInfo);
        $framework->smarty->display('bank/channel_edit.html');
    }
}