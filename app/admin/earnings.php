<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function add()
{

    if (IS_POST) {
        $title = $amount = $desc = $startTime = $endTime = $status = null;
        $requireFields = ['title', 'amounts', 'desc', 'startTime', 'endTime', 'status'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $field . '不能为空']);
        }
        $data['title'] = $title;
        $data['amount'] = I('post.amounts/d');
        $data['desc'] = $desc;
        $data['start_time'] = $startTime;
        $data['end_time'] = $endTime;
        $data['status'] = $status;
        $data['create_time'] = date('Y-m-d H:i:s');//注册时间
        $data['update_time'] = date('Y-m-d H:i:s');//注册时间
        
        try {
            $activityModel = new \Model\ConfigEarnings();
            $userId = $activityModel->add($data);
            if (!$userId) throw new \Exception('添加收益配置失败', 4011);
            ajaxReturn(['error' => 0, 'message' => '添加收益配置成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();

        $framework->smarty->display('earnings/add.html');
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
    $activityModel = new \Model\ConfigEarnings();
    $list = $activityModel->get()->resultArr();
    
    $framework->smarty->assign('list', $list);
    $framework->smarty->display('earnings/lst.html');
}

/**
 * 冻结/解冻功能
 * @pageroute
 */
function status()
{
    $id = I('get.id/d', 0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=earnings&a=index';
    if ($id) {
        $activityModel = new \Model\ConfigEarnings();
        if ($activityModel->switchStausById($id))
            redirect($goto, '2', '切换成功');
        else
            redirect($goto, '2', '切换失败');
    } else {
        redirect($goto, 2, '数据不合法');
    }

}

/**
 * @pageroute
 * 编辑企业账号
 */
function upd()
{
    $id = I('get.id/d', 0);
    if (IS_POST) {
        $title = $amount = $desc = $startTime = $endTime = $status = null;
        $requireFields = ['title', 'amounts', 'desc', 'startTime', 'endTime', 'status'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $field . '不能为空']);
        }
        $data['title'] = $title;
        $data['amount'] = I('post.amounts/d');
        $data['desc'] = $desc;
        $data['start_time'] = $startTime;
        $data['end_time'] = $endTime;
        $data['status'] = $status;
        $data['create_time'] = date('Y-m-d H:i:s');//注册时间
        $data['update_time'] = date('Y-m-d H:i:s');//注册时间

        try {
            $activityModel = new \Model\ConfigEarnings();
            //创建用户账号
            $userId = $activityModel->where(['id' => $id])->upd($data);
            if (!$userId)
                throw new \Exception('修改收益配置失败', 4011);
            ajaxReturn(['error' => 0, 'message' => '修改收益配置成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();
        $activityModel = new \Model\ConfigEarnings();
        $row = $activityModel->where(['id' => $id])->get()->rowArr();
        $framework->smarty->assign('item', $row);
        $framework->smarty->display('earnings/upd.html');
    }
}
