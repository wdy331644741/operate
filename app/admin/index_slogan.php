<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function add()
{

    if (IS_POST) {
        $title = $linkUrl = $pos = $startTime = $endTime = $status = null;
        $requireFields = ['title','linkUrl', 'pos', 'startTime', 'endTime', 'status'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $field . '不能为空']);
        }
// var_dump($title);exit;

        $sloganModel = new Model\MarketingIndex();

        $sloganModel->title = $title;
        $sloganModel->link_url = $linkUrl;
        $sloganModel->pos = $pos;
        $sloganModel->start_time = $startTime;
        $sloganModel->end_time = $endTime;
        $sloganModel->is_del = 0;
        $sloganModel->status = $status;
        $sloganModel->create_time = date('Y-m-d H:i:s');//注册时间

        try {

            $result = $sloganModel->save();
            if (!$result)
                throw new \Exception('添加slogan失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '添加slogan图成功']);

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
        $framework->smarty->display('slogan/add.html');
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
    $sloganModel = new \Model\MarketingIndex();
    $list = $sloganModel->get()->resultArr();
    // $storage = new Storage\Storage();
    // foreach ($list as $index => $item)
    //     $list[$index]['img_url'] = $storage->getViewUrl($item['img_url']);
    $framework->smarty->assign('list', $list);
    $framework->smarty->display('slogan/lst.html');
}

/**
 * 冻结/解冻功能
 * @pageroute
 */
function status()
{
    $id = I('get.id/d', 0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=slogan&a=index';
    if ($id) {
        $sloganModel = new \Model\MarketingIndex();
        if ($sloganModel->switchStausById($id))
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
        $title = $linkUrl = $pos = $startTime = $endTime = $status = null;
        $requireFields = ['title','linkUrl', 'pos', 'startTime', 'endTime', 'status'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');

            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $field . '不能为空']);
        }

        $data['title'] = $title;
        $data['link_url'] = $linkUrl;
        $data['pos'] = $pos;
        $data['start_time'] = $startTime;
        $data['end_time'] = $endTime;
        $data['status'] = $status;
        $data['update_time'] = date('Y-m-d H:i:s');//注册时间
// var_dump($data);exit("sads");

        try {
            $sloganModel = new \Model\MarketingIndex();
            //创建用户账号
            $userId = $sloganModel->where(['id' => $id])->upd($data);
            if (!$userId)
                throw new \Exception('修改slogan失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '修改slogan成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();
        // $storage = new Storage\Storage();
        $sloganModel = new \Model\MarketingIndex();
        $row = $sloganModel->where(['id' => $id])->get()->rowArr();
        // $row['img'] = $row['img_url'];
        // $row['img_url'] = $storage->getViewUrl($row['img_url']);
        // $urlReturn = $storage->getUploadUrl();
        // $url = '';
        // if ($urlReturn['status'] == 200) {
        //     $url = $urlReturn['msg'];
        // }
        // $framework->smarty->assign('url', $url);
        $framework->smarty->assign('item', $row);
        $framework->smarty->display('slogan/upd.html');
    }
}
