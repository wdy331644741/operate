<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/29
 * Time: 14:11
 */
/**
 * @pageroute
 * 节点列表
 */
function index()
{
    $framework = getFrameworkInstance();
    $nodeModel = new Model\AdminNode();
    $nodeList = $nodeModel->get()->resultArr();
    foreach ($nodeList as $k => $v) {
        $nodeList[$k]['nodeStatus'] = Model\Pub::$STATUS_MAP[$v['status']];
    }
    $nodeList = node_merges($nodeList);
    $framework->smarty->assign('nodeList', $nodeList);
    $framework->smarty->display('admin_node/index.html');
}

/**
 * @pageroute
 * 添加节点
 */
function add()
{
    $nodeModel = new Model\AdminNode();
    $errno = 100;
    if (IS_AJAX) {
        $nodeModel->status = I('post.status/d');
        if (empty($nodeModel->controller = strtolower(I('post.controller')))) {
            ajaxReturn(array('error' => $errno, 'msg' => 'controller不能为空'));
        }
        if (empty($nodeModel->action = strtolower(I('post.action')))) {
            ajaxReturn(array('error' => $errno, 'msg' => 'action不能为空'));
        }
        if (empty($nodeModel->remark = I('post.remark'))) {
            ajaxReturn(array('error' => $errno, 'msg' => '描述不能为空'));
        }
        $nodeModel->parent_id = I('post.pid/d');
        $nodeModel->url_host = I('post.url_host/d');
        $nodeModel->create_time = date('Y-m-d H:i:s');
        if ($nodeModel->save()) {
            ajaxReturn(array('error' => 200, 'msg' => '添加成功'));
        } else {
            ajaxReturn(array('error' => $errno, 'msg' => '添加失败'));
        }
    } else {
        $framework = getFrameworkInstance();
        $nodeList = $nodeModel->get()->resultArr();
        $framework->smarty->assign('nodeList', node_merges($nodeList));
        $framework->smarty->assign('status', Model\Pub::$STATUS_MAP);
        $framework->smarty->assign('host', C('RBAC_URL'));
        $framework->smarty->display("admin_node/add.html");
    }
}

/**
 * @pageroute
 * 修改节点
 */
function edit()
{
    $id = I('id/d');
    $errno = 100;
    $nodeModel = new Model\AdminNode($id);

    if (IS_AJAX) {
        $nodeModel->status = I('post.status/d');
        if ($id <= 0) {
            ajaxReturn(array('error' => $errno, 'msg' => '数据不合法'));
        }
        if (empty($nodeModel->controller = strtolower(I('post.controller')))) {
            ajaxReturn(array('error' => $errno, 'msg' => 'controller不能为空'));
        }
        if (empty($nodeModel->action = strtolower(I('post.action')))) {
            ajaxReturn(array('error' => $errno, 'msg' => 'action不能为空'));
        }
        if (empty($nodeModel->url = I('post.url'))) {
            ajaxReturn(array('error' => $errno, 'msg' => 'url不能为空'));
        }
        if (empty($nodeModel->remark = I('post.remark'))) {
            ajaxReturn(array('error' => $errno, 'msg' => '描述不能为空'));
        }
        $nodeModel->parent_id = I('post.pid/d');
        $nodeModel->url_host = I('post.host/d');
        $nodeModel->update_time = date('Y-m-d H:i:s');
        if ($nodeModel->save()) {
            ajaxReturn(array('error' => 200, 'msg' => '修改成功'));
        } else {
            ajaxReturn(array('error' => $errno, 'msg' => '修改失败'));
        }
    } else {
        if ($id <= 0) {
            redirect('?c=node&a=index', 2, '数据不合法');
        }
        $framework = getFrameworkInstance();
        $nodeList = $nodeModel->get()->resultArr();
        $framework->smarty->assign('nodeInfo', $nodeModel->getOneByPk($id));
        $framework->smarty->assign('nodeList', node_merges($nodeList));
        $framework->smarty->assign('host', C('RBAC_URL'));
        $framework->smarty->assign('status', Model\Pub::$STATUS_MAP);
        $framework->smarty->display("admin_node/edit.html");
    }
}

/**
 * @pageroute
 * 删除节点
 */
function del()
{
    $id = I('get.id/d');
    if ($id <= 0) {
        redirect('?c=node&a=index', 2, '数据不合法');
    }
    $nodeModel = new Model\AdminNode($id);
    $result=$nodeModel->where(array('parent_id'=>$id))->get()->resultArr();
    if($result){
        ajaxReturn(array('error' => 100, 'msg' => '该节点有子级,请禁用子级之后在禁用该节点'));
    }
    if ($nodeModel->status == Model\Pub::STATUS_ENABLE) {
        $nodeModel->status = Model\Pub::STATUS_DISABLE;
        $msg = Model\Pub::$STATUS_MAP[$nodeModel->status];
    } else {
        $nodeModel->status = Model\Pub::STATUS_ENABLE;
        $msg = Model\Pub::$STATUS_MAP[$nodeModel->status];
    }
    if ($nodeModel->save()) {
        redirect('?c=admin_node&a=index', 2, $msg . "成功");
    } else {
        redirect('?c=admin_node&a=index', 2, $msg . "失败");
    }

}

