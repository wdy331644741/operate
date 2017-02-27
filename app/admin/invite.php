<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function index()
{
    lst();
}

/**
 * @pageroute
 */
function lst()
{

    $framework = getFrameworkInstance();
    $sessionObj = new \Lib\Session();
    $page = I('get.p/d');
    $list = [];
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
        $authUserModel = new \Model\AuthUser();
        $nums = $authUserModel->where(['from_user_id' => $userId])->countNums();
        $pageObj = getPageObj($nums,$page);
        $list = $authUserModel->listTable(['from_user_id' => $userId], $pageObj->start, $pageObj->offset, "id desc")->resultArr();
        $page_num = $pageObj->createLink();
        $framework->smarty->assign('total',$nums);
        $framework->smarty->assign('pageNum',$page_num);
    }
    $framework->smarty->assign('sessionObj',$sessionObj);
    $framework->smarty->assign('list',$list);
    $framework->smarty->display('invite/lst.html');
}

