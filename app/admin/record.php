<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/20
 * Time: 16:40
 */
/**
 * @pageroute
 * 资金流水列表
 */
function index()
{
    $framework = getFrameworkInstance();
    $sessionObj = new \Lib\Session();
    $results= array();
    $page = I('get.p/d');
    //查询
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
            redirect('?c=user&a=lst');
        }
    }
    $userId = $sessionObj->get('userData.admin_user.serach.userId');
    if($userId)
    {
        $marginModel = new \Model\MarginRecord();
        //数据总数
        $nums = $marginModel->where(['user_id'=>$userId])->countNums();
        //分页类，返回object
        $paginObj = getPageObj($nums,$page);
        $results = $marginModel->listTable(['user_id'=>$userId], $paginObj->start, $paginObj->offset, "id desc")->resultArr();
        $page_num = $paginObj->createLink();
        $marginDiff = $marginModel->getMarginDiff($userId);
        $framework->smarty->assign('marginDiff',$marginDiff);//流水余额，和资产余额
        $framework->smarty->assign('total',$nums);
        $framework->smarty->assign('pageNum',$page_num);

    }
    $framework->smarty->assign('sessionObj',$sessionObj);
    $framework->smarty->assign('lists',$results);
    $framework->smarty->display('record/index.html');
}
/**
 * @pageroute
 * 详情
 */
function detail()
{
    $framework = getFrameworkInstance();
    $id = I('get.id');
    $marginModel = new \Model\MarginRecord();
    $info = $marginModel->where(array('id'=>$id))->get()->rowArr();
    $framework->smarty->assign('list',$info);
    $framework->smarty->display('record/detail.html');
}