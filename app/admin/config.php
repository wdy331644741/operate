<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");

/**
 * @pageroute
 * 资金管理配置列表
 */
function lst()
{
    $framework = getFrameworkInstance();
    $configModel = new \Model\ConfigCapital();
    $configList = $configModel->get()->resultArr();
    $framework->smarty->assign('list',$configList);
    $framework->smarty->display('config/lst.html');
}

/**
 * @pageroute
 * 资金管理配置更新
 */
function upd()
{
    $framework = getFrameworkInstance();
    $configModel = new \Model\ConfigCapital();
    $status = [];
    if(IS_POST)
    {
       $data = I('post.');
       if(!$data){
           ajaxReturn(['error' => 4000, 'message' => '配置值不能为空']);
       }
        try
        {
            foreach($data['data'] as $field=>$val)
            {
                if(!$val)
                    ajaxReturn(['error' => 4000, 'message' => $field.'配置值不能为空']);

                $status[$field] = $configModel->update(['config_value'=>$val],['name'=>$field]);
            }
            if($status)
            {
                foreach($status as $k=>$item)
                {
                    if($item ==='false')
                    {
                        throw new \Exception('更新配置失败', 4011);
                    }
                }
            }
            ajaxReturn(['error' => 0, 'message' => '更新配置成功']);
        }catch (\Exception $e)
        {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    }else
    {
        $configList = $configModel->get()->resultArr();
        //dump($configList);die;
        $framework->smarty->assign('list',$configList);
        $framework->smarty->display('config/upd.html');
    }
}
/**
 * @pageroute
 * 资金管理配置添加
 */
function add()
{
    if(IS_POST)
    {
        $name = $remark = $config_value = null;
        $requireFields = ['name', 'remark', 'config_value'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $$field . '不能为空']);
        }
        $data['name'] = $name;
        $data['remark'] = $remark;
        $data['config_value'] = $config_value;
        try
        {
            $configModel = new \Model\ConfigCapital();
            //创建资金管理配置
            $id = $configModel->add($data);
            if (!$id)
                throw new \Exception('添加配置失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '添加配置成功']);
        }catch (\Exception $e)
        {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }else
    {
        $framework = getFrameworkInstance();
        $framework->smarty->display('config/add.html');
    }
}