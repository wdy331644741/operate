<?php
/**
 * \Event::setEventCallback用于设置系统级事件回调
 * 1.事件名称由系统给出,不可乱写,瞎写.
 * 2.每个事件回调函数的参数都是系统固定给出,无法自定义
 * 3.事件的定义可以重复!!!相同的事件多个回调,将会被按定义的顺序挨个回调
 */


/**
 * 事件名:psr-4规范加载类被实例化后
 * 自动加载类完成加载之后 可以继续加载命名空间
 * \Event::setEventCallback('_after_loadAutoLoader', function ($autoloader) {
 *       //$autoloader->addNamespace("Lib", __FRAMEWORK_LIB_PATH__);
  *  });
 */



/**
 * 事件名:路由被解析执行前
 * 常见使用场景
 * 1.可以在此拦截路由做RBAC权限
 * 2.可以在此做路由转发
 *
 * 示例; 开发统一RBAC权限管理
 * 1.是否当前访问的是后台模块
 * 2.利用路由转发给统一的权限校验程序处理
 * 3.权限校验程序逻辑编写
 * \Event::setEventCallback('_before_parseRoute', function ($module, $controller, $action) {
 *           if ($module == "admin") {
 *          Route::execRoute("index/indexComment");
 *      }
 *  });
 */
\Event::setEventCallback('_before_parseRoute', function ($module, $controller, $action) {
    //如果当前访问的是后台, 且 访问不是登录页面
    //$_SESSION = array();
    if($module == 'admin' && (!Route::checkRoute('admin/login/index'))){
        $sessionObj = new \Lib\Session();
        $session = $sessionObj->get('userData.admin_user');
        if(!empty($session))
        {
            $framework = getFrameworkInstance();
            $rbac=new Lib\Rbac();
            if(array_key_exists('authList',$session))
            {
              $framework->smarty->assign('rbacList',$session['auth_list']);
            }else
            {
                $session['auth_list'] =  $rbac->getNodeIdByGroupId($session['role_id']);
                $sessionObj->set('userData.admin_user',$session);
               $framework->smarty->assign('rbacList',$session['auth_list']);
            }
            $controller=CONTROLLER;
            $action=ACTION;
            $NO_AUTH=array('index','login');
            if(!in_array(strtolower($controller),$NO_AUTH)){
                if(!$rbac->checkRbac($session['role_id'],$controller,$action)){
                    /*if(IS_AJAX)
                        ajaxReturn(['error'=>'100','msg'=>'你没有此权限,请联系管理员']);
                    redirect('?c=index&a=index',2,'你没有此权限,请联系管理员');*/
                }
            }
            //后台管理员名
            $userModel = new \Model\AuthUser();
            $where = 'create_time > '. '" ' .date('Y-m-d H:i:s',(time()-86400*10)) .' " '. ' AND  create_time <= '.'" ' .date('Y-m-d H:i:s').' "';
            $userDayNums=$userModel->where($where)->countNums();
            //根据权限判断查询
            $roleStatus = 1;//高级查询
            if(isset($session['user_role'])){
                $adminUserRole = $session['user_role'];
            }else{
                $adminUserRoleModel = new \Model\AdminUserRole();
                $adminUserRole = $adminUserRoleModel->where(['role_id'=>$session['role_id']])->get()->rowArr();
                $sessionObj->set('userData.admin_user.user_role',$adminUserRole);
            }
            if($adminUserRole['select_type']==1){
                $roleStatus = 2;
            }else{
                $roleStatus = 1;
            }
            $framework->smarty->assign('roleStatus',$roleStatus);
            $framework->smarty->assign('userNum',$userDayNums);
            $framework->smarty->assign('session',$sessionObj);
            $framework->smarty->assign('CURRENT_USER',$session['username']);
        }
        else
        {
            redirect("?c=login&a=index");
        }

    }
});

