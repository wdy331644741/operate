<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 17:36
 */
defined("__FRAMEWORKNAME__") or die("No permission to access!");

/**
 * 站内信列表
 * @pageroute
 */
function index()
{
    $framework = getFrameworkInstance();
    $results = [];//返回结果
    $sessionObj = new \Lib\Session();
    $page = I('get.p/d') ? I('get.p/d') : 1;
    $status = I('get.status') ? I('get.status') :'all';
    $type =I('post.type') != $sessionObj->get('userData.admin_user.serach.queryType') ? true :false;//判断查询的select
    $type2 =I('post.phone') != $sessionObj->get('userData.admin_user.serach.queryValue') ? true :false;//判断查询的查询条件
    if(I('post.type') && I('post.phone'))
    {
        if($type || $type2)
        {
            $sessionObj->set('userData.admin_user.serach.queryType',I('post.type'));
            $sessionObj->set('userData.admin_user.serach.queryValue',I('post.phone'));
            redirect('?c=user&a=index');
        }
    }
    $userId = $sessionObj->get('userData.admin_user.serach.userId');
    $jsonRpcObj = new \Lib\JsonRpcClient(config('RPC_API.msg'));
   //$jsonRpcObj = new \Lib\JsonRpcClient('http://dev.wangxiaofool.com/wl_message/App/web/message.php?c=msg');
    if($userId)
    {
        $messages = $jsonRpcObj->lstType(['user_id'=>$userId,'mtype'=>$status,'page'=>$page]);
        if(array_key_exists('error',$messages))
        {
            redirect('?c=user&a=index',2,'参数类型有错误，请重新查询!');
        }
        $results = $messages['result']['data']['lst'];
        $framework->smarty->assign('total',$messages['result']['data']['sum_message']);
        //分页
        if($messages['result']['data']['total_page'] > 1)
        {
            $config = [
                'baseurl'=>'admin.php?c=message&a=index&p='.$page.'&status='.$status,
                'total'=>$messages['result']['data']['sum_message'],
                'pagesize'=>C('PAGE_SIZE'),
                'current_page'=>$page];
            $pagination = new \Lib\Pagination($config);
            $page_num = $pagination->createLink();
            $framework->smarty->assign('pageNum',$page_num);
        }
    }
    $framework->smarty->assign('lists',$results);
    $framework->smarty->assign('sessionObj',$sessionObj);
    $framework->smarty->display('message/index.html');

}
