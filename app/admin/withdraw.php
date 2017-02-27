<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * 自动提现列表
 * @pageroute
 */
function autoLst()
{
    $framework = getFrameworkInstance();
    $marginModel = new \Model\MarginWithdraw();
    $totalNums = $marginModel->where(['is_manual'=>0])->countNums();
    $page = I('get.p/d');
    //分页类，返回object
    $paginObj = getPageObj($totalNums,$page);
    $results = $marginModel->listTable(['is_manual'=>0], $paginObj->start, $paginObj->offset, "id desc")->resultArr();
    $pageNum = $paginObj->createLink();
    $framework->smarty->assign('total',$totalNums);
    $framework->smarty->assign('pageNum',$pageNum);
    $framework->smarty->assign('lists',$results);
    $framework->smarty->display('withdraw/autoLst.html');
}
/**
 *@pageroute
 *人工提现列表
 */
function lst()
{
    $framework = getFrameworkInstance();
    $marginModel = new \Model\MarginWithdraw();
    $startTime = I('post.start_time');
    $endTime = I('post.end_time');
    $phone= I('post.phone');
    $pageCurrentNum = I('get.p');
    $where = 'is_manual = 1';
    if($startTime){
        $where .= " AND create_time >=".'\''.$startTime.'\'';
    }
    if($endTime)
    {
        $where .= " AND create_time <=".'\''.$endTime.'\'';
    }
    if($phone)
    {
        $where .= " AND phone =".'\''.$phone.'\'';
    }

    $totalNums = $marginModel->where($where)->countNums();
    $page = I('get.p/d');
    //分页类，返回object
    $paginObj = getPageObj($totalNums,$page);
    $results = $marginModel->listTable($where, $paginObj->start, $paginObj->offset, "id desc")->resultArr();
    $pageNum = $paginObj->createLink();
    $framework->smarty->assign('startTime',$startTime);
    $framework->smarty->assign('endTime',$endTime);
    $framework->smarty->assign('total',$totalNums);
    $framework->smarty->assign('pageNum',$pageNum);
    $framework->smarty->assign('lists',$results);
    $framework->smarty->assign('phone',$phone);
    $framework->smarty->assign('pageCurrentNum',$pageCurrentNum);

    $framework->smarty->display('withdraw/lst.html');
}
/**
 * @pageroute
 */
function verifyWithdraw()
{
    $framework = getFrameworkInstance();
    $marginModel = new \Model\MarginWithdraw();
    $id = I('post.id');
    $withdrawData = $marginModel->where(['id'=>$id])->get()->rowArr();
    if($withdrawData)
    {
        try
        {
            $params = ["withdrawId"=>$id,"sign"=>hash('sha256', $id . ACCOUNT_SECRET)];
            $obj = new App\service\rpcserverimpl\Common();
            $obj::localApiCall((object)$params, 'manualAudit', 'FundsRpcImpl');
        }catch (\Exception $e)
        {
            ajaxReturn(['error'=>100,'msg'=>$e->getMessage()]);
        }
        ajaxReturn(['error'=>200,'msg'=>'审核成功']);
    }
    ajaxReturn(['error'=>100,'msg'=>'未找到该数据']);
}

/**
 * @pageroute
 */
function auditRefuse()
{
    $framework = getFrameworkInstance();
    $marginModel = new \Model\MarginWithdraw();
    $id = I('post.id');
    $withdrawData = $marginModel->where(['id'=>$id])->get()->rowArr();
    if($withdrawData)
    {
        try
        {
            $params = [
                "withdrawId"=>$id,
                "sign"=>hash('sha256', $id . ACCOUNT_SECRET),
                "errorMsg"=>''
            ];
            $obj = new App\service\rpcserverimpl\Common();
            $obj::localApiCall((object)$params, 'refuseAudit', 'FundsRpcImpl');
        }catch (\Exception $e)
        {
            ajaxReturn(['error'=>100,'msg'=>$e->getMessage()]);
        }
        ajaxReturn(['error'=>200,'msg'=>'审核成功']);
    }
    ajaxReturn(['error'=>100,'msg'=>'未找到该数据']);
}