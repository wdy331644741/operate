<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 */
function add()
{

    if (IS_POST) {
        $gloabConfig = new Model\GloabConfig();

        $gloabConfig->key = I('post.key', '', 'trim');;
        $gloabConfig->value = json_encode($_POST['data'] );
        $gloabConfig->remark = I('post.remark', '', 'trim');
        $gloabConfig->create_time = date('Y-m-d H:i:s');//注册时间
        // var_dump($gloabConfig->value );exit;

        try {
            // $data['key'] = I('post.key', '', 'trim');
            // $data['remark'] = I('post.remark', '', 'trim');
            // $data['update_time'] = date('Y-m-d H:i:s');
            // $data['value'] = json_encode($_POST['data'] );

            $result = $gloabConfig->save();
            if (!$result)
                throw new \Exception('添加node失败', 4011);

            //更新redis
            $redis = getReidsInstance();
            $redis->hset('operate_gloab_conf',$gloabConfig->key,$gloabConfig->value );
            $remindInfo = $redis->hgetall('operate_gloab_conf');
            ajaxReturn(['error' => 0, 'message' => '添加配置成功']);

        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();

        $framework->smarty->display('gloab_config/add.html');
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
    $bannerModel = new \Model\GloabConfig();
    $list = $bannerModel->get()->resultArr();

    $framework->smarty->assign('list', $list);
    $framework->smarty->display('gloab_config/lst.html');
}

/**
 * @pageroute
 * 编辑企业账号
 */
function upd()
{
    $id = I('get.id/d', 0);
    if (IS_POST) {
        $gloabConfig = new Model\GloabConfig();

        // $gloabConfig->key = I('post.key', '', 'trim');
        // $gloabConfig->value = json_encode($_POST['data'] );
        // $gloabConfig->remark = I('post.remark', '', 'trim');
        // $gloabConfig->create_time = date('Y-m-d H:i:s');
        // var_dump($gloabConfig->value );exit;

        try {

            $data['key'] = I('post.key', '', 'trim');
            $data['remark'] = I('post.remark', '', 'trim');
            $data['update_time'] = date('Y-m-d H:i:s');
            $data['value'] = json_encode($_POST['data'] );

            $result = $gloabConfig->where(['id' => $id])->upd($data);
            if (!$result)
                throw new \Exception('修改失败', 4011);
            //更新redis
            $redis = getReidsInstance();
            $redis->hset('operate_gloab_conf',$data['key'],$data['value'] );
            $remindInfo = $redis->hgetall('operate_gloab_conf');
            ajaxReturn(['error' => 0, 'message' => '修改成功']);

        } catch (\Exception $e) {
            ajaxReturn(['error' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    } else {
        $framework = getFrameworkInstance();
        $bannerModel = new \Model\GloabConfig();
        $row = $bannerModel->where(['id' => $id])->get()->rowArr();
        $json_data = json_decode( stripslashes ($row['value'] ),true);

        $framework->smarty->assign('item', $row);
        $framework->smarty->assign('json_data', $json_data);
        $framework->smarty->display('gloab_config/upd.html');
    }
}
