<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");

/**
 * @pageroute
 * 推广员列表
 */
function index()
{
    $sessionObj = new \Lib\Session();
    $framework = getFrameworkInstance();
    $promoterModel = new \Model\PromoterList();

    $configEarnings = new \Model\ConfigEarnings();

    $results = [];
    $data['queryType'] = I('post.type');
    $data['queryValue'] = I('post.phone');
    if($data['queryValue']){
        switch ($data['queryType']) {
            case 'user_id':
                # code...
                $where = 'auth_id ='. $data['queryValue'];
                break;
            case 'user_name':
                # code...
                $where = 'username ='. $data['queryValue'];
                break;
            case 'phone_num':
                # code...
                $where = 'phone ='. $data['queryValue'];
                break;
            default:
                break;
        }
        $results = $promoterModel->where($where)->get()->resultArr();
    }else{
        $results = $promoterModel->get()->resultArr();
    }
    $sessionObj->set('userData.admin_user.serach.queryType',$data['queryType']);
    $sessionObj->set('userData.admin_user.serach.queryValue',$data['queryValue']);
 
    $earningNum = $configEarnings->getAllInfoById();
    // var_dump($earningNum);
    foreach ($results as & $value) {
        @$value['percent'] = $earningNum[$value['earnings_id']];
    }
    // var_export($results);exit;

    $framework->smarty->assign('sessionObj',$sessionObj);
    $framework->smarty->assign('lists',$results);
    $framework->smarty->display('promoter/index.html');
}

/**
 * @pageroute
 * 推广员审核通过
 */
function checkpass(){
    $id = I('get.id/d',0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=promoter&a=index';
    if($id)
    {
        $promoterModel = new \Model\PromoterList();
        $users = $promoterModel->where(['apply_id'=>$id])->get()->row();
        if($users)
        {
            $data=['status'=>1];
            if($promoterModel->where(['apply_id'=>$id])->upd($data)){
                redirect($goto,'2',"成功");
            }else{
                redirect($goto,'2',"失败");
            }
        }else
        {
            redirect($goto,'2',"该推广员不存在");
        }
    }
    else
    {
        redirect($goto, 2, '数据不合法');
    }
}
/**
 * @pageroute
 * 推广员审核不通过
 */
function checkrefuse(){
    $id = I('get.id/d',0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=promoter&a=index';
    if($id)
    {
        $promoterModel = new \Model\PromoterList();
        $users = $promoterModel->where(['apply_id'=>$id])->get()->row();
        if($users)
        {
            $data=['status'=>2];
            if($promoterModel->where(['apply_id'=>$id])->upd($data)){
                redirect($goto,'2',"成功");
            }else{
                redirect($goto,'2',"失败");
            }
        }else
        {
            redirect($goto,'2',"该推广员不存在");
        }
    }
    else
    {
        redirect($goto, 2, '数据不合法');
    }
}

/**
 * @pageroute
 * 推广员审核
 */
function check(){
    $id = I('get.id/d',0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=promoter&a=index';
    if($id)
    {
        $promoterModel = new \Model\PromoterList();
        $users = $promoterModel->where(['apply_id'=>$id])->get()->row();
        if($users)
        {
            if($users->status ==1){
                $data=['status'=>0];
            }elseif($users->status ==0){
                $data=['status'=>1];
            }
            if($promoterModel->where(['apply_id'=>$id])->upd($data)){
                redirect($goto,'2',"成功");
            }else{
                redirect($goto,'2',"失败");
            }
        }else
        {
            redirect($goto,'2',"该推广员不存在");
        }
    }
    else
    {
        redirect($goto, 2, '数据不合法');
    }
}

/**
 * @pageroute
 * 推广员每日新增
 */
function dailyadd(){
    $framework = getFrameworkInstance();
    $statistics = new \Model\PromoterStatistics();
    $data = $statistics->get()->resultArr();
    // $data = array();//展示数据
    $framework->smarty->assign('lists',$data);
    $framework->smarty->display('promoter/dailyadd.html');
}

/**
 * @pageroute
 * 推广员刪除
 */
function del(){
    $id = I('get.id/d',0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=promoter&a=index';

    if($id)
    {
        $promoterModel = new \Model\PromoterList();
        $users = $promoterModel->where(['apply_id'=>$id])->get()->row();
        if($users)
        {
            $res = $promoterModel->delete(['apply_id'=>$id]);
            if($res){
                redirect($goto,'2',"成功");
            }else{
                redirect($goto,'2',"失败");
            }
        }else
        {
            redirect($goto,'2',"该推广员不存在");
        }
    }
}