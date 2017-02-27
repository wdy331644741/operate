<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function lst()
{
    $framework = getFrameworkInstance();
    $page = I('get.p/d');
    $authUserModel = new \Model\AwardExtend();
    $nums = $authUserModel->countNums();
    $pageObj = getPageObj($nums,$page);
    $list = $authUserModel->listTable('', $pageObj->start, $pageObj->offset, "id desc")->resultArr();
    $page_num = $pageObj->createLink();
    $framework->smarty->assign('total',$nums);
    $framework->smarty->assign('pageNum',$page_num);
    $framework->smarty->assign('list',$list);
    $framework->smarty->display('award/lst.html');
}

/**
 * @pageroute
 */
function add()
{
    $framework = getFrameworkInstance();
    if(IS_POST)
    {
        $awardModel = new \Model\AwardExtend();
        $user = $award_type = $award_id = $remark = null;
        $requireFields = ['user', 'award_type','award_id'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $field . '不能为空']);
        }
        $data['user'] = $user;
        $data['award_type'] = $award_type;
        $data['award_id'] = $award_id;
        $data['remark'] = I('post.remark', '', 'trim');
        $data['send_status'] = 0;
        $data['create_time'] = date("Y-m-d H:i:s");
        if($awardModel->add($data))
        {
            $obj = new App\service\rpcserverimpl\Common();
            $obj::messageBroadcast('manualPrize',$data);
            ajaxReturn(['error' => 0, 'message' => '发放成功']);
        }
        else
        {
            ajaxReturn(['error' => 0, 'message' => '发放失败']);
        }


    }else
    {
        $framework->smarty->display('award/add.html');
    }
}

/**
 * @pageroute
 * 选择发放类型
 */
function awardType()
{
    $type = I('post.type');
    if(!$type)
        ajaxReturn(['error' => 4000, 'message' => '奖品类型不能为空']);
    //类型1为体验金 2为加息劵
    if($type ==1)
        $awardModel = new \Model\AwardExperience();
    else
        $awardModel = new \Model\AwardInterestcoupon();

    $list = $awardModel->get()->resultArr();

    ajaxReturn(['error' => 0, 'message'=>$list]);
}