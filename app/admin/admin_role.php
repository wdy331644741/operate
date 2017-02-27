<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/29
 * Time: 11:37
 */

/**
 * @pageroute
 * 角色列表
 */
function index()
{
    $framework = getFrameworkInstance();
    $roleModel = new Model\AdminRole();
    $config = [
        "baseurl" => U('admin.php', ['c' => 'admin_role', 'a' => 'index']),
        'total' => $roleModel->countNums(),    //设置记录总数
        'pagesize' => C('PAGE_SIZE'),       //设置每页数量
        'current_page' => I('get.p/d', 1), //设置当前页码
    ];
    $pagination = new Lib\Pagination($config);//分页类
    $roleList = $roleModel->listTable('', $pagination->start, $pagination->offset, "create_time desc")->resultArr();
    foreach ($roleList as $k => $v) {
        $roleList[$k]['roleStatus'] = Model\Pub::$STATUS_MAP[$v['status']];
    }
    //  var_dump($roleList);
    $framework->smarty->assign("pagination_link", $pagination->createLink());//方法：创建分页链接
    $framework->smarty->assign('roleList', $roleList);
    $framework->smarty->display('admin_role/index.html');
}

/**
 * @pageroute
 * 添加角色
 */
function add()
{
    $errno = 100;
    $framework = getFrameworkInstance();
    if (IS_AJAX) {
        $roleModel = new Model\AdminRole();
        $roleModel->status = I('post.status/d');
        if (empty($roleModel->name = I('post.name'))) {
            ajaxReturn(array('error' => $errno, 'msg' => '角色不能为空'));
        }
        $roleModel->remark = I('post.remark');
        $roleModel->create_time = date('Y-m-d H:i:s');
        if ($roleModel->save()) {
            ajaxReturn(array('error' => 200, 'msg' => '添加成功'));
        } else {
            ajaxReturn(array('error' => $errno, 'msg' => '添加失败'));
        }


    } else {
        $framework->smarty->assign('status', Model\Pub::$STATUS_MAP);
        $framework->smarty->display('admin_role/add.html');
    }
}

/**
 * @pageroute
 * 修改角色
 */
function edit()
{
    $errno = 100;
    $id = I('id/d');
    $roleModel = new Model\AdminRole($id);
    if (IS_AJAX) {
        if ($id <= 0) {
            ajaxReturn(array('error' => $errno, 'msg' => '数据不合法'));
        }
        $roleModel->status = I('post.is_del/d');

        $roleModel->status = I('post.status/d');
        if (empty($roleModel->name = I('post.name'))) {
            ajaxReturn(array('error' => $errno, 'msg' => '角色不能为空'));
        }
        $roleModel->remark = I('post.remark');
        $roleModel->update_time = date('Y-m-d H:i:s');
        if ($roleModel->save()) {
            ajaxReturn(array('error' => 200, 'msg' => '修改成功'));
        } else {
            ajaxReturn(array('error' => $errno, 'msg' => '修改失败'));
        }


    } else {

        if ($id <= 0) {
            redirect('?c=role&a=index', 2, '数据不合法');
        }
        $framework = getFrameworkInstance();
        $framework->smarty->assign('roleInfo', $roleModel->getOneByPk($id));
        $framework->smarty->assign('status', Model\Pub::$STATUS_MAP);
        $framework->smarty->display('admin_role/edit.html');
    }
}

/**
 * @pageroute
 * 删除角色
 */
function del()
{
    $id = I('get.id/d');
    if ($id <= 0) {
        redirect('?c=role&a=index', 2, '数据不合法');
    }
    $roleModel = new Model\AdminRole($id);
    if ($roleModel->status == Model\Pub::STATUS_ENABLE) {
        $roleModel->status = Model\Pub::STATUS_DISABLE;
        $msg = Model\Pub::$STATUS_MAP[$roleModel->status];
    } else {
        $roleModel->status = Model\Pub::STATUS_ENABLE;
        $msg = Model\Pub::$STATUS_MAP[$roleModel->status];
    }
    if ($roleModel->save()) {
        redirect('?c=role&a=index', 2, $msg . "成功");
    } else {
        redirect('?c=role&a=index', 2, $msg . "失败");
    }

}

/**
 * @pageroute
 * 分配权限
 */
function rbac()
{
    $id = I('id/d');

    if (IS_AJAX) {
        $nodeId = I('post.');
        $roleModel = new Model\AdminRole($nodeId['group_id']);
        $roleModel->rule = implode(',', $nodeId['node']);
        if ($roleModel->save()) {
            ajaxReturn(array('error' => 200, 'msg' => '授权成功'));
        } else {
            ajaxReturn(array('error' => 100, 'msg' => '授权失败'));
        }
    } else {
        if ($id <= 0) {
            redirect('?c=role&a=index', 2, '数据不合法');
        }
        $roleModel = new Model\AdminRole();
        $framework = getFrameworkInstance();
        $nodeModel = new Model\AdminNode();
        //获取权限的节点
        $ruleList = $roleModel->getOneByPk($id);
        //获取所有节点
        $nodeList = $nodeModel->where(array('status'=>Model\Pub::STATUS_ENABLE))->get()->resultArr();
        $nodeList = node_merges($nodeList, explode(',', $ruleList['rule']));
        $framework->smarty->assign('group_id', $id);
        $framework->smarty->assign('nodeList', $nodeList);
        $framework->smarty->display('admin_role/rbac.html');
    }
}
/**
 * @pageroute
 * 分配展示用户信息权限
 */
function edit_user_role()
{
    $framework = getFrameworkInstance();
    $adminUserRoleModel = new \Model\AdminUserRole();//字段展示分配表
    $adminRoleModel = new \Model\AdminRole();//角色表
    if(IS_POST)
    {
        $postData = I('post.');
        $sessionObj = new \Lib\Session();
        $session = $sessionObj->get('userData.admin_user');
        if($postData)
        {
            foreach($postData as $key=>$val){
                if(!isset($val['id_number'])){
                    $val['id_number'] = 1;
                }
                if(!isset($val['phone'])){
                    $val['phone'] = 1;
                }
                //存入数据库的字段
                $adminUserRoleModel->setItem($key,$val);
            }
            $adminUserRole = $adminUserRoleModel->where(['role_id'=>$session['role_id']])->get()->rowArr();
            $sessionObj->set('userData.admin_user.user_role',$adminUserRole);
            redirect('?c=admin_role&a=index','2','操作成功');
        }else{
            die('没有任何数据要提交');
        }
    }else{
        $data = $adminUserRoleModel->get()->resultArr();
        $roleList = $adminRoleModel->fields('id,name')->get()->resultArr();
        $framework->smarty->assign('result', $data);
        $framework->smarty->assign('roleList', $roleList);
        $framework->smarty->display('admin_role/user_role.html');
    }

}



