<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 * 用户新增数据
 */
function index(){
    $framework = getFrameworkInstance();
    $userModel = new \Model\AuthUser(); //'create_time > ' .date('Y-m-d',(time()-86400*10)). ' AND create_time <='.date('Y-m-d')
    $where = 'create_time > '. '" ' .date('Y-m-d H:i:s',(time()-86400*10)) .' " '. ' AND  create_time <= '.'" ' .date('Y-m-d H:i:s').' "';
   // dump($where);die;
    $userDayNums=$userModel->where($where)->countNums();
    //dump($data);die;
    $framework->smarty->assign('userNum',$userDayNums);
    $framework->smarty->display('index/index.html');
}