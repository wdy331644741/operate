<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function add()
{
    if (IS_POST) {
        $title = $amount = $minAmount = $maxAmount = $days = $effectiveEnd = $limitDesc = $limitNode = $status = $repeat = null;
        $requireFields = ['title', 'amount', 'minAmount', 'maxAmount', 'days', 'effectiveEnd', 'limitDesc', 'limitNode', 'status', 'repeat'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $$field . '不能为空']);
        }

        if ($minAmount && $maxAmount) {
            $data['amount'] = 0;
            $data['min_amount'] = $minAmount;
            $data['max_amount'] = $maxAmount;
            $data['amount_type'] = \Model\AwardExperience::TYPE_RAND;
        } else {
            $data['amount'] = $amount;
            $data['min_amount'] = 0;
            $data['max_amount'] = 0;
            $data['amount_type'] = \Model\AwardExperience::TYPE_NORMAL;
        }
        $data['title'] = $title;
        $data['days'] = $days;
        $data['effective_end'] = $effectiveEnd;
        $data['limit_desc'] = $limitDesc;
        $data['limit_node'] = $limitNode;
        $data['status'] = $status;
        $data['repeat'] = $repeat;
        $data['create_time'] = date('Y-m-d H:i:s');//注册时间

        try {
            $expModel = new \Model\AwardExperience();
            //创建用户账号
            $id = $expModel->add($data);
            if (!$id)
                throw new \Exception('添加experience失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '添加experience成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();

        $nodeModel = new \Model\AwardNode();
        $nodeListQuery = $nodeModel->get()->resultArr();
        $nodeList = array_combine(array_column($nodeListQuery, 'id'), $nodeListQuery);
        $framework->smarty->assign('nodeList', $nodeList);
        $framework->smarty->display('experience/add.html');
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
    $experienceUserModel = new \Model\AwardExperience();
    $list = $experienceUserModel->get()->resultArr();

    $nodeModel = new \Model\AwardNode();
    $nodeListQuery = $nodeModel->get()->resultArr();
    $nodeList = array_combine(array_column($nodeListQuery, 'id'), $nodeListQuery);
    foreach ($list as &$val) {
        foreach ($nodeList as $nodel) {
            if ($val['limit_node'] == $nodel['id']) {
                $val['node_title'] = $nodel['title'];
            }
        }
    }
    $framework->smarty->assign('list', $list);
    $framework->smarty->assign('nodeList', $nodeList);
    $framework->smarty->display('experience/lst.html');
}

/**
 * 冻结/解冻功能
 * @pageroute
 */
function status()
{
    $id = I('get.id/d', 0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=experience&a=index';
    if ($id) {
        $bannerModel = new \Model\AwardExperience();
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
    $id = I('get.id/d', 0);
    if (IS_POST) {
        $title = $amount = $minAmount = $maxAmount = $days = $effectiveEnd = $limitDesc = $limitNode = $status = $repeat = null;
        $requireFields = ['title', 'amount', 'minAmount', 'maxAmount', 'days', 'effectiveEnd', 'limitDesc', 'limitNode', 'status', 'repeat'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $$field . '不能为空']);
        }

        if ($minAmount && $maxAmount) {
            $data['amount'] = 0;
            $data['min_amount'] = $minAmount;
            $data['max_amount'] = $maxAmount;
            $data['amount_type'] = \Model\AwardExperience::TYPE_RAND;
        } else {
            $data['amount'] = $amount;
            $data['min_amount'] = 0;
            $data['max_amount'] = 0;
            $data['amount_type'] = \Model\AwardExperience::TYPE_NORMAL;
        }
        $data['title'] = $title;
        $data['days'] = $days;
        $data['effective_end'] = $effectiveEnd;
        $data['limit_desc'] = $limitDesc;
        $data['limit_node'] = $limitNode;
        $data['status'] = $status;
        $data['repeat'] = $repeat;
        $data['update_time'] = date('Y-m-d H:i:s');//注册时间
        try {
            $expModel = new \Model\AwardExperience();
            //创建用户账号
            $id = $expModel->where(['id' => $id])->upd($data);
            if (!$id)
                throw new \Exception('修改experience失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '修改experience成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();
        $expModel = new \Model\AwardExperience();
        $row = $expModel->where(['id' => $id])->get()->rowArr();

        $nodeModel = new \Model\AwardNode();
        $nodeListQuery = $nodeModel->get()->resultArr();
        $nodeList = array_combine(array_column($nodeListQuery, 'id'), $nodeListQuery);

        $framework->smarty->assign('item', $row);
        $framework->smarty->assign('nodeList', $nodeList);
        $framework->smarty->display('experience/upd.html');
    }
}
