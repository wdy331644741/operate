<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function add()
{
    if (IS_POST) {
        $title = $imgUrl = $linkUrl = $sort = $startTime = $endTime = $status = null;
        $requireFields = ['title', 'imgUrl','linkUrl', 'sort', 'startTime', 'endTime', 'status'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $field . '不能为空']);
        }
        $data['activity_name'] = I('post.activity_name','trim');
        $data['title'] = $title;
        $data['img_url'] = $imgUrl;
        $data['link_url'] = $linkUrl;
        $data['sort'] = $sort;
        $data['start_time'] = $startTime;
        $data['end_time'] = $endTime;
        $data['status'] = $status;
        $data['create_time'] = date('Y-m-d H:i:s');//注册时间


        try {
            $activityModel = new \Model\MarketingActivity();
            //创建用户账号
            $userId = $activityModel->add($data);
            if (!$userId)
                throw new \Exception('添加activity失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '添加activity成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();
        $storage = new Storage\Storage();
        $urlReturn = $storage->getUploadUrl();
        $url = '';
        if ($urlReturn['status'] == 200) {
            $url = $urlReturn['msg'];
        }
        $framework->smarty->assign('url', $url);
        $framework->smarty->display('activity/add.html');
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
    $activityModel = new \Model\MarketingActivity();
    $list = $activityModel->get()->resultArr();
    $storage = new Storage\Storage();
    foreach ($list as $index => $item){
        $list[$index]['img_url'] = $storage->getViewUrl($item['img_url']);
        $list[$index]['link_url'] = config('RPC_API.wechat').$list[$index]['link_url'];
    }
    $framework->smarty->assign('list', $list);
    $framework->smarty->display('activity/lst.html');
}

/**
 * 冻结/解冻功能
 * @pageroute
 */
function status()
{
    $id = I('get.id/d', 0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=activity&a=index';
    if ($id) {
        $activityModel = new \Model\MarketingActivity();
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
        $title = $imgUrl = $linkUrl = $sort = $startTime = $endTime = $status = null;
        $requireFields = ['title', 'imgUrl','linkUrl', 'sort', 'startTime', 'endTime', 'status'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $field . '不能为空']);
        }
        $data['activity_name'] = I('post.activity_name','trim');
        $data['title'] = $title;
        $data['img_url'] = $imgUrl;
        $data['link_url'] = $linkUrl;
        $data['sort'] = $sort;
        $data['start_time'] = $startTime;
        $data['end_time'] = $endTime;
        $data['status'] = $status;
        $data['update_time'] = date('Y-m-d H:i:s');//注册时间


        try {
            $activityModel = new \Model\MarketingActivity();
            //创建用户账号
            $userId = $activityModel->where(['id' => $id])->upd($data);
            if (!$userId)
                throw new \Exception('修改activity失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '修改activity成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();
        $storage = new Storage\Storage();
        $activityModel = new \Model\MarketingActivity();
        $row = $activityModel->where(['id' => $id])->get()->rowArr();
        $row['img'] = $row['img_url'];
        $row['img_url'] = $storage->getViewUrl($row['img_url']);
        $urlReturn = $storage->getUploadUrl();
        $url = '';
        if ($urlReturn['status'] == 200) {
            $url = $urlReturn['msg'];
        }
        $framework->smarty->assign('url', $url);
        $framework->smarty->assign('item', $row);
        $framework->smarty->display('activity/upd.html');
    }
}
