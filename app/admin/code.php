<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/22
 * Time: 18:41
 */
/**
 * @pageroute
 * 短信验证码列表
 */
function index()
{
    $framework = getFrameworkInstance();
    $sessionObj = new \Lib\Session();
    $codeModel = new \Model\AuthCode();
    $results = [];
    $page = I('get.p/d') ? I('get.p/d') : 1;
    if(I('post.type') && I('post.phone'))
    {
        if(
            I('post.type') != $sessionObj->get('userData.admin_user.serach.queryType')
            ||
            I('post.phone') != $sessionObj->get('userData.admin_user.serach.queryValue')
        )
        {
            $sessionObj->set('userData.admin_user.serach.queryType',I('post.type'));
            $sessionObj->set('userData.admin_user.serach.queryValue',I('post.phone'));
            redirect('?c=user&a=index');
        }
    }
    $userId = $sessionObj->get('userData.admin_user.serach.userId');
    $authUserModel = new \Model\AuthUser();
    $authUser = $authUserModel->fields('phone')->where(['id'=>$userId])->get()->row();
    if($authUser)
    {
        $jsonRpcObj = new \Lib\JsonRpcClient(config('RPC_API.msg'));
        $messages = $jsonRpcObj->smsList([ "phone"=>$authUser->phone,"page"=>$page]);
        //dump($messages);die;
        if(array_key_exists('error',$messages))
        {
            redirect('?c=user&a=index',2,'参数类型有错误，请重新查询!');
        }
        $results = $messages['result']['data']['smsLogLst'];
        $results = maskData($results);
        $framework->smarty->assign('total',$messages['result']['data']['sms_sum']);
        //分页
        if($messages['result']['data']['total_page'] > 1)
        {
            $config = [
                'baseurl'=>'admin.php?c=message&a=index&p='.$page,
                'total'=>$messages['result']['data']['sms_sum'],
                'pagesize'=>C('PAGE_SIZE'),
                'current_page'=>$page];
            $pagination = new \Lib\Pagination($config);
            $page_num = $pagination->createLink();
            $framework->smarty->assign('pageNum',$page_num);
        }
    }
    $framework->smarty->assign('sessionObj',$sessionObj);
    $framework->smarty->assign('lists',$results);
    $framework->smarty->display('code/index.html');
}