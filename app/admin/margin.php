<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * Created by PhpStorm.
 * User: sf
 * Date: 2016/6/20
 * Time: 11:53
 */
/**
 * 用户资产列表
 * @pageroute
 *
 */
function index()
{
    $framework = getFrameworkInstance();
    $results = array();//返回结果
    $sessionObj = new \Lib\Session();
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
    if($userId) {
        $marginModel = new \Model\MarginMargin();
        $results = $marginModel->getMarginSurvey($userId);
        $results['user_id'] = $userId;
    }
    $framework->smarty->assign('sessionObj',$sessionObj);
    $framework->smarty->assign('lists',$results);
    $framework->smarty->display('margin/index.html');
}
/**
 * 编辑
 * @pageroute
 */
function edit()
{
    $framework = getFrameworkInstance();

    if(IS_AJAX)
    {
        $status = '100';
        $id = I('post.user_id');
        if ($id <= 0)
            ajaxReturn(array('error' => $status, 'msg' => '数据不合法'));
        $data = checkInfo();
        if($data['status']== false)
            ajaxReturn(array('error'=>$status,'msg'=>'填写数据不符合规范'));
        $marginModel = new \Model\MarginMargin($id);
        $marginModel->total_amount = $data['data']['total_amount'];
        $marginModel->avaliable_amount = $data['data']['avaliable_amount'];
        $marginModel->principal_amount = $data['data']['principal_amount'];
        $marginModel->withdrawing_amount = $data['data']['withdrawing_amount'];
        $marginModel->invset_amount = $data['data']['invset_amount'];
        if($marginModel->save())
            ajaxReturn(array('error'=>'200','msg'=>'修改成功'));
        else
            ajaxReturn(array('error'=>$status,'msg'=>'修改失败'));
    }else
    {
    $marginModel = new \Model\MarginMargin();
    $uid = I('get.user_id');
    $marginINfo = $marginModel->where(array('user_id'=>$uid))->get()->rowArr();
    $framework->smarty->assign('list',$marginINfo);
    $framework->smarty->display('margin/edit.html');
    }
}
function checkInfo()
{
    $info = I('post.');
    $status = true;
    $data['total_amount'] = $info['total_amount'] ? $info['total_amount'] : '';
    $data['avaliable_amount'] = $info['avaliable_amount'] ? $info['avaliable_amount'] : '';
    $data['principal_amount'] = $info['principal_amount'] ? $info['principal_amount'] : '';
    $data['withdrawing_amount'] = $info['withdrawing_amount'] ? $info['withdrawing_amount'] : '';
    $data['invset_amount'] = $info['invset_amount'] ? $info['invset_amount'] : '';
   /* if($data['total_amount'] =='0')//当总资产为0时，其他都不能有值
    {
        if($data['avaliable_amount'] != '' || $data['principal_amount'] != '' ||
            $data['withdrawing_amount'] != '' || $data['invset_amount'] != '')
        {
            $status = false;
        }
    }*/
    return array('status'=>$status,'data'=>$data);
}