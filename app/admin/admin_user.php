<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 * 用户列表
 */
function index()
{
    //echo "add";exit;

    $framework = getFrameworkInstance();
    $adminUserModel = new Model\AdminUser();
    $roleModel = new Model\AdminRole();
    $config = [
        "baseurl" => U('admin.php', ['c' => 'admin_user', 'a' => 'index']),
        'total' => $adminUserModel->countNums(),    //设置记录总数
        'pagesize' => C('PAGE_SIZE'),       //设置每页数量
        'current_page' => I('get.p/d', 1), //设置当前页码
    ];
    $pagination = new Lib\Pagination($config);
    $userList = $adminUserModel->listTable('', $pagination->start, $pagination->offset, "create_time desc")->resultArr();
    $role_id = '';
    foreach ($userList as $key => $value) {
        $role_id .= $value['role_id'] . ",";
    }
    //var_dump($userList);
    //echo $role_id;
    $roleList = $roleModel->whereIn('id', rtrim($role_id, ','))->get()->resultArr();
    //var_dump($roleList);
    foreach ($userList as $k => $v) {
        $userList[$k]['userStatus'] = Model\Pub::$STATUS_MAP[$v['is_del']];
        foreach ($roleList as $key => $val) {
            if ($v['role_id'] == $val['id']) {
                $userList[$k]['roleName'] = $val['name'];
                continue;
            }
        }
    }

    $framework->smarty->assign("pagination_link", $pagination->createLink());
    $framework->smarty->assign('userList', $userList);
    $framework->smarty->display('admin_user/list.html');
}

/**
 * @pageroute
 * 添加管理员
 */
function add()
{
    $errno = 100;
    $framework = getFrameworkInstance();
    if (IS_AJAX) {
        $adminUserModel = new Model\AdminUser();
        $adminUserModel->is_del = I('post.is_del/d');
        if (empty($adminUserModel->name = I('post.username'))) {
            ajaxReturn(array('error' => $errno, 'msg' => '用户名不能为空'));
        } else if ($adminUserModel->getUserIdByName($adminUserModel->name)) {
            ajaxReturn(array('error' => $errno, 'msg' => '昵称已经存在'));
        }
        if (empty($password = I('post.password'))) {
            ajaxReturn(array('error' => $errno, 'msg' => '密码不能为空'));
        }

        if (!is_numeric($adminUserModel->role_id = I('post.role/d'))) {
            ajaxReturn(array('error' => $errno, 'msg' => '请选择角色'));
        }
        $adminUserModel->create_time = date('Y-m-d H:i:s');
        $adminUserModel->is_form = '1';
        $adminUserModel->password = md5($password);
       // $adminUserModel->salt = rand(1000,9000);
        
        if ($adminUserModel->save()) {
            ajaxReturn(array('error' => 200, 'msg' => '添加成功'));
        } else {
            ajaxReturn(array('error' => $errno, 'msg' => '添加失败'));
        }


    } else {
        $roleModel = new Model\AdminRole();
        $framework->smarty->assign('roleList', $roleModel->where(array('status' => Model\Pub::STATUS_ENABLE))->get()->resultArr());
        $framework->smarty->assign('status', Model\Pub::$STATUS_MAP);
        $framework->smarty->display('admin_user/add.html');
    }
}

/**
 * @pageroute
 * 修改管理员
 */
function edit()
{
    $errno = 100;
    $id = I('id/d');

    $adminUserModel = new Model\AdminUser($id);
    if (IS_AJAX) {
        if ($id <= 0) {
            ajaxReturn(array('error' => $errno, 'msg' => '数据不合法'));
        }
        $adminUserModel->is_del = I('post.is_del/d');
        if (empty($adminUserModel->name = I('post.username'))) {
            ajaxReturn(array('error' => $errno, 'msg' => '用户名不能为空'));
        }
        if (!is_numeric($adminUserModel->role_id = I('post.role/d'))) {
            ajaxReturn(array('error' => $errno, 'msg' => '请选择角色'));
        }
        $adminUserModel->update_time = date('Y-m-d H:i:s');
        if ($adminUserModel->save()) {
            ajaxReturn(array('error' => 200, 'msg' => '修改成功'));
        } else {
            ajaxReturn(array('error' => $errno, 'msg' => '修改失败'));
        }

    } else {

        if ($id <= 0) {
            redirect('?c=admin_user&a=index', 2, '数据不合法');
        }
        $framework = getFrameworkInstance();
        $roleModel = new Model\AdminRole();
        $framework->smarty->assign('roleList', $roleModel->where(array('status' => Model\Pub::STATUS_ENABLE))->get()->resultArr());
        $framework->smarty->assign('userInfo', $adminUserModel->getOneByPk($id));
        $framework->smarty->assign('status', Model\Pub::$STATUS_MAP);
        $framework->smarty->display('admin_user/edit.html');
    }
}

/**
 * @pageroute
 * 删除管理员
 */
function del()
{
    $id = I('get.id/d');
    if ($id <= 0) {
        redirect('?c=admin_user&a=index', 2, '数据不合法');
    }
    $adminUserModel = new Model\AdminUser($id);
    if ($adminUserModel->is_del == Model\Pub::STATUS_ENABLE) {
        $adminUserModel->is_del = Model\Pub::STATUS_DISABLE;
        $msg = Model\Pub::$STATUS_MAP[$adminUserModel->is_del];
    } else {
        $adminUserModel->is_del = Model\Pub::STATUS_ENABLE;
        $msg = Model\Pub::$STATUS_MAP[$adminUserModel->is_del];
    }
    if ($adminUserModel->save()) {
        redirect('?c=admin_user&a=index', 2, $msg . "成功");
    } else {
        redirect('?c=admin_user&a=index', 2, $msg . "失败");
    }

}
/**
 * @pageroute
 * 用户列表
 */
function resetPassword()
{
    $id = I('id/d');
    $adminUserModel = new Model\AdminUser($id);
    $errno = 100;
    if (IS_POST) {
        if ($id <= 0) {
            ajaxReturn(array('error' => $errno, 'msg' => '数据不合法'));
        }
        if (empty($password = I('password'))) {
            ajaxReturn(array('error' => $errno, 'msg' => '新密码不能为空'));
        }
        if (empty($repassword = I('repassword'))) {
            ajaxReturn(array('error' => $errno, 'msg' => '确认密码不能为空'));
        }
        if ($password != $repassword) {
            ajaxReturn(array('error' => $errno, 'msg' => '确认密码和新密码不一致'));
        }
        $adminUserModel->password = md5($password);
        if ($adminUserModel->save()) {
            ajaxReturn(array('error' => 200, 'msg' => '密码重置成功'));
        } else {
            ajaxReturn(array('error' => 200, 'msg' => '密码重置失败'));
        }
    } else {
        if ($id <= 0) {
            redirect('?c=admin_user&a=index', 2, '数据不合法');
        }
        $framework = getFrameworkInstance();
        $framework->smarty->assign('userInfo', $adminUserModel->getOneByPk($id));
        $framework->smarty->display('admin_user/reset.html');
    }
}



