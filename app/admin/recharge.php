<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 * 充值列表
 */
function lst()
{
    $framework = getFrameworkInstance();
    $sessionObj = new \Lib\Session();
    $results = array();
    $page = I('get.p/d');
    //查询
    if(I('post.type') && I('post.phone'))
    {
        if(
            I('post.type') != $sessionObj->get('userData.admin_user.serach.queryType')
            ||
            I('post.phone') != $sessionObj->get('userDataadmin_user..serach.queryValue')
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
        $marginModel = new \Model\MarginRecharge();
        //数据总数
        $nums = $marginModel->where(['user_id'=>$userId])->countNums();
        $pageObj = getPageObj($nums,$page);
        $results = $marginModel->listTable(['user_id'=>$userId], $pageObj->start, $pageObj->offset, "id desc")->resultArr();
        $page_num = $pageObj->createLink();
        $framework->smarty->assign('total',$nums);
        $framework->smarty->assign('pageNum',$page_num);
    }

    $framework->smarty->assign('sessionObj',$sessionObj);
    $framework->smarty->assign('lists',$results);
    $framework->smarty->display('recharge/lst.html');
}
/**
 * @pageroute
 * 详情
 */
function detail()
{
    $framework = getFrameworkInstance();
    $id = I('get.id');
    $marginModel = new \Model\MarginRecharge();
    $marginInfo = $marginModel->where('id='.$id)->get()->rowArr();
    $framework->smarty->assign('list',$marginInfo);
    $framework->smarty->display('recharge/detail.html');
}

/**
 * @pageroute
 * 修改充值订单状态
 */
function syncOrderUp()
{
    $id = I('get.id');
    if(!$id) {
        redirect('?c=recharge&a=lst',2,'该条信息不存在');
    }
    $marginRechargeModel = new \Model\MarginRecharge();
    $marginRechargeInfo = $marginRechargeModel->where(['id'=>$id])->get()->rowArr();
    if(!$marginRechargeInfo){
        redirect('?c=recharge&a=index',2,'该条信息不存在');
    }
    $params = ['uuid'=>$marginRechargeInfo['uuid'],'sign'=>hash('sha256', $marginRechargeInfo['uuid'] . ACCOUNT_SECRET)];
    $obj = new App\service\rpcserverimpl\Common();
    try{
        $obj::localApiCall((object)$params, 'syncOrderStatus', 'InsideRpcImpl');
    }catch (\Exception $e){
        redirect('?c=recharge&a=lst',2,$e->getMessage());
    }
    redirect('?c=recharge&a=lst',2,'发送成功,处理中');
}