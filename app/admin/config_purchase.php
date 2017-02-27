<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");

/**
 * @pageroute
 * 资金管理配置列表
 */
function lst()
{
    $framework = getFrameworkInstance();
    $configModel = new \Model\ConfigPurchase();
    $configList = $configModel->get()->resultArr();
    $framework->smarty->assign('list',$configList);
    $framework->smarty->display('config_purchase/lst.html');
}

/**
 * @pageroute
 * 资金管理配置更新
 */
function upd()
{
    $framework = getFrameworkInstance();
    $configModel = new \Model\ConfigPurchase();
    if(IS_POST)
    {
       $amount = $status = $id = null;
        $requireFields = ['amount', 'status','id'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $$field . '不能为空']);
        }
        $data['amount'] = $amount;
        $data['purchase_amount'] = $amount;
        $data['status'] = $status==0 ?0 : 1;
        try
        {
            $updateId = $configModel->where(['id'=>$id])->upd($data);
            if (!$updateId)
                throw new \Exception('修改配置失败', 4011);

            ajaxReturn(['error' => 0, 'message' => '更新配置成功']);
        }catch (\Exception $e)
        {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    }else
    {
        $id = I('get.id');
        if(!$id)
            die('请正确打开该页面');
        $configList = $configModel->where(['id'=>$id])->get()->rowArr();
        $framework->smarty->assign('list',$configList);
        $framework->smarty->display('config_purchase/upd.html');
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
        $amount= $status =  null;
        $requireFields = ['amount', 'status'];
        foreach ($requireFields as $field) {
            $$field = I('post.' . $field, '', 'trim');
            if ('' === $$field)
                ajaxReturn(['error' => 4000, 'message' => $$field . '不能为空']);
        }
        $data['amount'] = $amount;
        $data['purchase_amount'] = $amount;
        $data['status'] = $status==0 ? 0 :1;
        $data['create_time'] = date('Y-m-d H:i:s');
        try
        {
            $configModel = new \Model\ConfigPurchase();
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
        $framework->smarty->display('config_purchase/add.html');
    }
}