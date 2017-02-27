<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function index()
{
    $app = getFrameworkInstance();
    if (IS_POST) {
        $name = I('post.username');
        $password = I('post.password');
        $userModel = new Model\AdminUser();
        $user_id = $userModel->getUserIdByName($name);
        if ($user_id) {
            $userModel->initArData($user_id);
            if ($userModel->is_del == \Model\AdminUser::DEL_TRUE) {
                redirect('?c=login&a=index', 2, '该用户已被禁用');
            }
            //密码验证
            if ($userModel->password == md5($password)) {
                $userModel->last_time = date('Y-m-d H:i:s');
                $ip = getIp();
                $userModel->last_ip = ip2long($ip);
                $userModel->save();
                //设置登录cookie数据
                $sessionObj = new Lib\Session();
                $data['username'] = $userModel->name;
                $data['role_id'] = $userModel->role_id;
                $data['userid'] = $userModel->id;
                $sessionObj->set('userData.admin_user',$data);
                redirect('?c=index&a=index');
            } else {
                redirect('?c=login&a=index', 2, '密码不正确');
            }
        } else {
            redirect('?c=login&a=index', 2, '密码不正确');
        }
    } else {
        $app->smarty->display('login.html');
    }

}

/**
 * 退出
 * @pageroute
 */
function logout()
{
    $sessionObj = new Lib\session();
    $sessionObj->del();
    redirect('?c=login&a=index');
}
