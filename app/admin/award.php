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
function unsendAward()
{
    $sendId = I('get.sid');
    $authUserModel = new \Model\AwardExtend($sendId);
    if(empty($authUserModel)){
        ajaxReturn(['error' => 400, 'message' => '记录不存在']);
    }
    $authUserModel->send_status = 2;
    $authUserModel->save();
    redirect("?c=award&a=lst");
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
        if($id = $awardModel->add($data))
        {
            $awardModel->dealRecord($id);
            ajaxReturn(['error' => 0, 'message' => '发放成功']);
        } else {
            ajaxReturn(['error' => 0, 'message' => '发放失败']);
        }
    }else {
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
    //类型1为体验金 2为加息劵 3 提现次数
    if($type ==1) {
        $awardModel = new \Model\AwardExperience();
    } elseif($type==2){
        $awardModel = new \Model\AwardInterestcoupon();
    } elseif ($type==3){
        $awardModel = new \Model\AwardWithdraw();
    }

    $list = $awardModel->where(['status'=>1])->get()->resultArr();

    ajaxReturn(['error' => 0, 'message'=>$list]);
}
/**
 * @pageroute
 */
function detail()
{
    $id = I('get.id',0,'intval');
    $model = new \Model\AwardHandRecord();
    $list = $model->getListByAwardId($id);
    $framework = getFrameworkInstance();

    $framework->smarty->assign('list',$list);
    $framework->smarty->assign('total',count($list));
    $framework->smarty->display('award/detail.html');
}
/**
 * @pageroute
 */
function retry()
{
    $id = I('get.id',0,'intval');
    $awardHandModel = new \Model\AwardHandRecord();
    $awardModel = new \Model\AwardExtend();
    $record = $awardHandModel->where(['id'=>$id])->get()->rowArr();
    $res = $awardModel->send(
        $record['award_type'],
        $record['user_id'],
        $record['award_id']);
    if ($res['is_ok']){
        $awardHandModel->update(['status' => 1], ['id' => $id]);
        ajaxReturn(['error' => 200, 'msg'=>'重试成功']);
    }else{
        ajaxReturn(['error' => 100, 'msg'=>$res['msg']]);
    }
}