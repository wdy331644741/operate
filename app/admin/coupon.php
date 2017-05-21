<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function add()
{
    if (IS_POST) {

        $title = $rate = $days = $effectiveEnd = $limitDesc = $limitNode = $status = null;
        $requireFields = ['title', 'rate', 'days', 'effectiveEnd', 'limitDesc','limitNode', 'status'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $$field . '不能为空']);
        }

        $data['coupon'] = I('post.coupon','','trim');
        $data['title'] = $title;
        $data['rate'] = $rate;
        $data['days'] = $days;
        $data['effective_end'] = $effectiveEnd;
        $data['limit_desc'] = $limitDesc;
        $data['limit_node'] = $limitNode;
        $data['status'] = $status;
        $data['create_time'] = date('Y-m-d H:i:s');//注册时间

        try {
            $couponModel = new \Model\AwardInterestcoupon();
            //创建用户账号
            $id = $couponModel->add($data);
            if (!$id)
                throw new \Exception('添加coupon失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '添加coupon成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();

        $nodeModel = new \Model\AwardNode();
        $nodeListQuery = $nodeModel->get()->resultArr();
        $nodeList =  array_combine(array_column($nodeListQuery,'id'),$nodeListQuery);
        $framework->smarty->assign('nodeList', $nodeList);
        $framework->smarty->display('coupon/add.html');
    }
}

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
    $couponUserModel = new \Model\AwardInterestcoupon();
    $list = $couponUserModel->get()->resultArr();

    $nodeModel = new \Model\AwardNode();
    $nodeListQuery = $nodeModel->get()->resultArr();
    $nodeList =  array_combine(array_column($nodeListQuery,'id'),$nodeListQuery);
    foreach($list as &$val)
    {
        foreach($nodeList as $nodel)
        {
            if($val['limit_node'] == $nodel['id'])
            {
                $val['node_title'] = $nodel['title'];
            }
        }
    }
    $framework->smarty->assign('list', $list);
    $framework->smarty->assign('nodeList', $nodeList);
    $framework->smarty->display('coupon/lst.html');
}

/**
 * 冻结/解冻功能
 * @pageroute
 */
function status()
{
    $id = I('get.id/d', 0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=coupon&a=index';
    if ($id) {
        $bannerModel = new \Model\AwardInterestcoupon();
        if ($bannerModel->switchStausById($id))
            redirect($goto, '2', '切换成功');
        else
            redirect($goto, '2', '切换失败');
    } else {
        redirect($goto, 2, '数据不合法');
    }

}

/**
 * @pageroute
 */
function upd()
{
    $id = I('get.id/d',0);
    if (IS_POST) {
        $title = $rate = $days = $effectiveEnd = $limitDesc = $limitNode = $status = null;
        $requireFields = ['title', 'rate', 'days', 'effectiveEnd', 'limitDesc','limitNode', 'status'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $$field . '不能为空']);
        }


        $data['title'] = $title;
        $data['rate'] = $rate;
        $data['days'] = $days;
        $data['effective_end'] = $effectiveEnd;
        $data['limit_desc'] = $limitDesc;
        $data['limit_node'] = $limitNode;
        $data['status'] = $status;
        $data['update_time'] = date('Y-m-d H:i:s');//注册时间

        try {
            $couponModel = new \Model\AwardInterestcoupon();
            //创建用户账号
            $id = $couponModel->where(['id'=>$id])->upd($data);
            if (!$id)
                throw new \Exception('修改coupon失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '修改coupon成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();
        $couponModel = new \Model\AwardInterestcoupon();
        $row = $couponModel->where(['id'=>$id])->get()->rowArr();

        $nodeModel = new \Model\AwardNode();
        $nodeListQuery = $nodeModel->get()->resultArr();
        $nodeList = array_combine(array_column($nodeListQuery,'id'),$nodeListQuery);

        $framework->smarty->assign('item', $row);
        $framework->smarty->assign('nodeList', $nodeList);
        $framework->smarty->display('coupon/upd.html');
    }
}
