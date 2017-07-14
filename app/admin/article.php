<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function add()
{
    if (IS_POST) {
        $title = $imgUrl = $cateNode = $content = $sort = $status = null;
        $requireFields = ['title','imgUrl','cateNode','content', 'sort', 'status'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $$field . '不能为空']);
        }

        $data['title'] = $title;
        $data['img_url'] = $imgUrl?:'';
        $data['cate_node'] = $cateNode;
        $data['content'] = htmlspecialchars($content);
        $data['sort'] = $sort;
        $data['status'] = $status;
        $data['create_time'] = date('Y-m-d H:i:s');//注册时间
        $data['res_name'] = I('post.resource_name', '', 'trim');
        $data['res_url'] = I('post.resource_url', '', 'trim');

        try {
            $articleModel = new \Model\MarketingArticle();
            //创建用户账号
            $id = $articleModel->add($data);
            if (!$id)
                throw new \Exception('添加article失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '添加article成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();
        $nodeModel = new \Model\MarketingArticleNode();
        $nodeListQuery = $nodeModel->get()->resultArr();
        $nodeList =  array_combine(array_column($nodeListQuery,'id'),$nodeListQuery);
        $storage = new Storage\Storage();
        $urlReturn = $storage->getUploadUrl();
        $url = '';
        if ($urlReturn['status'] == 200) {
            $url = $urlReturn['msg'];
        }
        $framework->smarty->assign('url', $url);
        $framework->smarty->assign('nodeList', $nodeList);
        $framework->smarty->display('article/add.html');
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
    $type = I('get.type', 'notice', 'trim');
    $framework = getFrameworkInstance();
    $articleUserModel = new \Model\MarketingArticle();
    $articleNode = new \Model\MarketingArticleNode();
    $noticeCate = $articleNode->where("`name` = '{$type}'")->get()->rowArr();
    $list = $articleUserModel->where("`cate_node` = {$noticeCate['id']}")->get()->resultArr();

    $nodeModel = new \Model\MarketingArticleNode();
    $nodeListQuery = $nodeModel->get()->resultArr();
    $nodeList =  array_combine(array_column($nodeListQuery,'id'),$nodeListQuery);

    $storage = new Storage\Storage();
    if($list)
    {
        foreach($list as &$val)
        {
            foreach($nodeList as $nodel)
            {
                if($val['cate_node'] == $nodel['id'])
                {
                    $val['cate_title'] = $nodel['title'];
                }
            }
            $val['img_url'] = $storage->getViewUrl($val['img_url']);
        }
    }
    $framework->smarty->assign('list', $list);
    $framework->smarty->assign('nodeList', $nodeList);
    $framework->smarty->display('article/lst.html');
}

/**
 * 冻结/解冻功能
 * @pageroute
 */
function status()
{
    $id = I('get.id/d', 0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=article&a=index';
    if ($id) {
        $bannerModel = new \Model\MarketingArticle();
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
        $title = $imgUrl = $cateNode = $content = $sort = $status = null;
        $requireFields = ['title','imgUrl','cateNode','content', 'sort', 'status'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $$field . '不能为空']);
        }

        $data['title'] = $title;
        $data['img_url'] = $imgUrl;
        $data['cate_node'] = $cateNode;
        $data['content'] = htmlspecialchars($content);
        $data['sort'] = $sort;
        $data['status'] = $status;
        $data['update_time'] = date('Y-m-d H:i:s');//注册时间

        try {
            $articleModel = new \Model\MarketingArticle();
            //创建用户账号
            $id = $articleModel->where(['id'=>$id])->upd($data);
            if (!$id)
                throw new \Exception('修改article失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '修改article成功']);
        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();
        $articleModel = new \Model\MarketingArticle();
        $storage = new Storage\Storage();
        $row = $articleModel->where(['id'=>$id])->get()->rowArr();
        $row['img'] = $row['img_url'];
        $row['img_url'] = $storage->getViewUrl($row['img_url']);
        $urlReturn = $storage->getUploadUrl();
        $url = '';
        if ($urlReturn['status'] == 200) {
            $url = $urlReturn['msg'];
        }
        $nodeModel = new \Model\MarketingArticleNode();
        $nodeListQuery = $nodeModel->get()->resultArr();
        $nodeList = array_combine(array_column($nodeListQuery,'id'),$nodeListQuery);
        $framework->smarty->assign('url', $url);
        $framework->smarty->assign('item', $row);
        $framework->smarty->assign('nodeList', $nodeList);
        $framework->smarty->display('article/upd.html');
    }
}
