<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 10:08
 */
/**
 * 银行列表
 * @pageroute
 */
function index()
{
    $framework = getFrameworkInstance();
    $page = I('get.p/d', 1);
    $type = I('get.type') ? I('get.type') : '1';
    $data = [$page,100,$type];
    $action ='lists';//服务端列表方法
    $jsonRPCClient = new Lib\JsonRpcClient(BANKJSONSERVICEURL);
    $result = call_user_func_array(array($jsonRPCClient,$action),$data);
    $result = checkData($result);
    $channel = getChannel();
    if($result && is_array($result))
    {
        $num = count($result['list']);
        foreach($result['list'] as &$val)
        {
            foreach($channel['list'] as $v)
            {
                if($v['channel_id'] == $val['pc_channel_id'])
                {
                    $val['pc_channel_name'] = $v['channel_name'];
                }
                if($v['channel_id'] == $val['app_channel_id'])
                {
                    $val['app_channel_name'] = $v['channel_name'];
                }
            }
        }
        $data = getChannel();//渠道列表
        $framework->smarty->assign('result', $data['list']);
        $framework->smarty->assign('imgurl', BANKIMAGEURL);
        $framework->smarty->assign('total', $num);
        $framework->smarty->assign('lists', $result['list']);
    }
    $framework->smarty->display('bank/index.html');
}
/**
 * @pageroute
 * 增加银行
 */
function add()
{
    $framework = getFrameworkInstance();
    if(IS_AJAX)
    {
        $bankInfo = I('post.');
        if(!$bankInfo)
            ajaxReturn(array('error'=>'100','msg'=>'提交的信息不全'));
        if(!(array_key_exists('is_pc_display',$bankInfo)))
            $bankInfo['is_pc_display'] =0;
        if(!(array_key_exists('is_app_display',$bankInfo)))
            $bankInfo['is_app_display'] = 0;
        $bankInfo['bank_logo'] = '';
        $data = [$bankInfo['bank_name'],$bankInfo['bank_code'],$bankInfo['bank_logo'],$bankInfo['is_pc_display'],$bankInfo['is_app_display'],htmlspecialchars_decode($bankInfo['pc_limit'])];
        $jsonRPCClient = new Lib\JsonRpcClient(BANKJSONSERVICEURL);
        $result = call_user_func_array(array($jsonRPCClient,'create'),$data);
        addLogs($data,'adminBankAdd');
        if(!(array_key_exists('error_code',$result)))
            ajaxReturn(array('error'=>'200','msg'=>'添加成功'));
        else
            ajaxReturn(array('error'=>'100','msg'=>'添加失败'));
    }else
    {
        $result = getChannel();
        $framework->smarty->assign('baseHost',C('BASE_HOST'));
        $framework->smarty->assign('lists',$result['list']);
        $framework->smarty->display('bank/add.html');
    }
}
/**
 * 获取渠道
 */
function getChannel()
{
    $jsonRPCClient = new Lib\JsonRpcClient(CHANNELSERVICEURL);
    $data= call_user_func_array(array($jsonRPCClient,'lists'),['1','20']);
    return checkData($data);
}
/**
 * 修改
 * @pageroute
 */
function edit()
{
    $framework =  getFrameworkInstance();
    if(IS_AJAX)
    {
        $bankInfo = I('post.');
        if(!$bankInfo)
            ajaxReturn(array('error'=>'100','msg'=>'提交的信息不全'));
        $data = [$bankInfo['id'],$bankInfo['bank_name'],$bankInfo['bank_code'],$bankInfo['bank_logo_old'],$bankInfo['is_display'],htmlspecialchars_decode($bankInfo['pc_limit']),$bankInfo['type']];
        $jsonRPCClient = new Lib\JsonRpcClient(BANKJSONSERVICEURL);
        $result = call_user_func_array(array($jsonRPCClient,'modify'),$data);
        addLogs($data,'adminBankEdit');
        if(!(array_key_exists('error_code',$result)))
            ajaxReturn(array('error'=>'200','msg'=>'修改成功'));
        else
            ajaxReturn(array('error'=>'100','msg'=>'修改失败'));
    }else {
        $data = I('get.');
        $data['type'] = I('get.type') ? I('get.type') : '1';
        $data['pc_limit'] = htmlspecialchars_decode($data['pc_limit']);
        $result = getChannel();//渠道列表
        $framework->smarty->assign('imgUrl', BANKIMAGEURL);
        $framework->smarty->assign('result', $data);
        $framework->smarty->assign('lists', $result['list']);
        $framework->smarty->display('bank/edit.html');
    }
}
/**
 * 设置渠道银行
 * @pageroute
 */
function set_channel()
{
    $framework = getFrameworkInstance();
    if(IS_AJAX)
    {
      $channelInfo = I('post.');
      if(!$channelInfo)
        ajaxReturn(array('error'=>'100','msg'=>'提交的信息不全'));
       $data = array($channelInfo['bank_id'],$channelInfo['channel_id'],$channelInfo['code'],$channelInfo['type'],$channelInfo['first_quota'],$channelInfo['times_quota'],$channelInfo['days_quota']);
       $jsonRPCClient = new Lib\JsonRpcClient(BANKJSONSERVICEURL);
       $result = call_user_func_array(array($jsonRPCClient,'set_channel'),$data);
       addLogs($data,'adminBankSetChannel');
       if(!(array_key_exists('error_code',$result)))
            ajaxReturn(array('error'=>'200','msg'=>'设置银行渠道成功'));
       else
            ajaxReturn(array('error'=>'100','msg'=>'设置银行渠道失败'));
    }else
    {
        if(I('get.extra') =='1')
        {
            $data['bank_id'] = I('get.id');
            $data['type'] = I('get.type') ? I('get.type') : 1;
        }elseif(I('get.extra')=='2')
        {
            $data = I('get.');
        }
        $channel = getChannel();
        $framework->smarty->assign('data', $data);
        $framework->smarty->assign('lists',$channel['list']);
        $framework->smarty->display('bank/set_channel.html');
    }
}
//检查返回的数据
function checkData($data)
{
    if(array_key_exists('error_code',$data)){
        return array();
    }else{
        return $data;
    }
}

/**
 * 切换渠道
 * @pageroute
 */
function switch_channel()
{
    $info = I('post.info');
    if(!$info)
        ajaxReturn(array('error'=>'100','msg'=>'参数错误'));
    $data = explode(',',$info);
    if(!($data['2']))
    {
        $data['2'] = '1';
    }
    $jsonRPCClient = new Lib\JsonRpcClient(BANKJSONSERVICEURL);
    $result = call_user_func_array(array($jsonRPCClient,'switch_channel'),$data);
    addLogs($data,'adminBnakSwitchChannel');
    if(!(array_key_exists('error_code',$result)))
        ajaxReturn(array('error'=>'200','msg'=>'切换渠道成功'));
    else
        ajaxReturn(array('error'=>'100','msg'=>'切换渠道失败'));
}
/**
 * 银行操作加入日志
 */
function addLogs($data,$filename=''){
    $sessionObj = new \Lib\Session();
    $session = $sessionObj->get('userData.admin_user');
    logs($session['username'],$filename);
    logs($data,$filename);
    return true;
}
/**
 * 渠道上下线
 * @pageroute
 */
function bankchannel_offline()
{
    $info = I('post.');
    if(!$info)
        ajaxReturn(array('error'=>'100','msg'=>'参数错误'));
    $data['bank_id'] = $info['bank_id'];
    $data['channel_id'] = $info['channel_id'];
    $data['type'] = $info['type'];
    if($info['is_offline']==2){
        $data['is_offline'] =1;
    }else{
        $data['is_offline'] =2;
    }
    $jsonRPCClient = new Lib\JsonRpcClient(BANKJSONSERVICEURL);
    $result = call_user_func_array(array($jsonRPCClient,'bankchannel_offline'),$data);
    addLogs($data,'adminBnakbankchannel_offline');
    if(!(array_key_exists('error',$result)))
        ajaxReturn(array('error'=>'200','msg'=>'成功',));
    else
        ajaxReturn(array('error'=>'100','msg'=>'失败'));
}
