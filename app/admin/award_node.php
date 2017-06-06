<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function add()
{

    if (IS_POST) {
        $title = $name = null;
        $requireFields = ['title', 'name'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $field . '不能为空']);
        }


        $bannerModel = new Model\AwardNode();

        $bannerModel->title = $title;
        $bannerModel->name = $name;
        
        $bannerModel->create_time = date('Y-m-d H:i:s');//注册时间

        try {

            $result = $bannerModel->save();
            if (!$result)
                throw new \Exception('添加node失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '添加banner图成功']);

        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();

        $framework->smarty->display('award_node/add.html');
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
    $bannerModel = new \Model\AwardNode();
    $list = $bannerModel->get()->resultArr();

    $framework->smarty->assign('list', $list);
    $framework->smarty->display('award_node/lst.html');
}

/**
 * @pageroute
 * 编辑企业账号
 */
function upd()
{
    $id = I('get.id/d', 0);
    if (IS_POST) {
        $title = $name = null;
        $requireFields = ['title', 'name'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $field . '不能为空']);
        }
        $data['title'] = $title;
        $data['name'] = $name;

        try {
            $bannerModel = new \Model\AwardNode();
            //创建用户账号
            $userId = $bannerModel->where(['id' => $id])->upd($data);
            if (!$userId)
                throw new \Exception('修改node失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '修改banner成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();
        $bannerModel = new \Model\AwardNode();
        $row = $bannerModel->where(['id' => $id])->get()->rowArr();
        $framework->smarty->assign('item', $row);
        $framework->smarty->display('award_node/upd.html');
    }
}
