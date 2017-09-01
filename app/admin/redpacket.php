<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function add()
{
    if (IS_POST) {
        $title = $max_split = $usetime_start = $usetime_end = $limitDesc = $limitNode = $status = $repeat = $day_repeat = null;
        $requireFields = ['title', 'max_split', 'usetime_start', 'usetime_end', 'limitDesc', 'limitNode', 'status', 'repeat', 'day_repeat'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $field . '不能为空']);
        }

        $amount = I('post.amount', '', 'trim');
        $min_amount = I('post.min_amount', '', 'trim');
        $max_amount = I('post.max_amount', '', 'trim');
        if (!empty($amount)) {
            $data['amount'] = $amount;
            $data['min_amount'] = 0;
            $data['max_amount'] = 0;
        } else {
            $data['amount'] = 0;
            $data['min_amount'] = $min_amount;
            $data['max_amount'] = $max_amount;
        }
        $data['title'] = $title;
        $data['usetime_start'] = $usetime_start;
        $data['usetime_end'] = $usetime_end;
        $data['limit_desc'] = $limitDesc;
        $data['limit_node'] = $limitNode;
        $data['status'] = $status;
        $data['repeat'] = $repeat;
        $data['day_repeat'] = $day_repeat;
        $data['max_counts'] = I('post.max_counts', '', 'trim');
        $data['create_time'] = date('Y-m-d H:i:s');
        try {
            $expModel = new \Model\AwardRedpacket();
            //创建用户账号
            $id = $expModel->add($data);
            if (!$id)
                throw new \Exception('添加redpacket失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '添加redpacket成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();

        $nodeModel = new \Model\AwardNode();
        $nodeListQuery = $nodeModel->get()->resultArr();
        $nodeList = array_combine(array_column($nodeListQuery, 'id'), $nodeListQuery);
        $framework->smarty->assign('nodeList', $nodeList);
        $framework->smarty->display('redpacket/add.html');
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
    $redpacketUserModel = new \Model\AwardRedpacket();
    $list = $redpacketUserModel->get()->resultArr();

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
    unset($val);
    $framework->smarty->assign('list', $list);
    $framework->smarty->assign('nodeList', $nodeList);
    $framework->smarty->display('redpacket/lst.html');
}

/**
 * 冻结/解冻功能
 * @pageroute
 */
function status()
{
    $id = I('get.id/d', 0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=redpacket&a=index';
    if ($id) {
        $bannerModel = new \Model\AwardRedpacket();
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
        $title = $max_split = $usetime_start = $usetime_end = $limit_desc = $limitNode = $status = $repeat = $day_repeat = $max_counts = null;
        $requireFields = ['title', 'max_split', 'usetime_start', 'usetime_end', 'limit_desc', 'limitNode', 'status', 'repeat', 'day_repeat', 'max_counts'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $field . '不能为空']);
        }

        $amount = I('post.amount', '', 'trim');
        $min_amount = I('post.min_amount', '', 'trim');
        $max_amount = I('post.max_amount', '', 'trim');
        if (!empty($amount)) {
            $data['amount'] = $amount;
            $data['min_amount'] = 0;
            $data['max_amount'] = 0;
        } else {
            $data['amount'] = 0;
            $data['min_amount'] = $min_amount;
            $data['max_amount'] = $max_amount;
        }
        $data['title'] = $title;
        $data['max_split'] = $max_split;
        $data['usetime_start'] = $usetime_start;
        $data['usetime_end'] = $usetime_end;
        $data['limit_desc'] = $limit_desc;
        $data['limit_node'] = $limitNode;
        $data['status'] = $status;
        $data['repeat'] = $repeat;
        $data['day_repeat'] = $day_repeat;
        $data['max_counts'] = $max_counts;
        $data['update_time'] = date('Y-m-d H:i:s');
        try {
            $expModel = new \Model\AwardRedpacket();
            //创建用户账号
            $id = $expModel->where(['id' => $id])->upd($data);
            if (!$id)
                throw new \Exception('修改redpacket失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '修改redpacket成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();
        $expModel = new \Model\AwardRedpacket();
        $row = $expModel->where(['id' => $id])->get()->rowArr();

        $nodeModel = new \Model\AwardNode();
        $nodeListQuery = $nodeModel->get()->resultArr();
        $nodeList = array_combine(array_column($nodeListQuery, 'id'), $nodeListQuery);

        $framework->smarty->assign('item', $row);
        $framework->smarty->assign('nodeList', $nodeList);
        $framework->smarty->display('redpacket/upd.html');
    }
}
