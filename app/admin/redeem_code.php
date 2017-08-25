<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");


/**
 * @pageroute
 */
function index()
{
    $fw = getFrameworkInstance();
    $model = new  \Model\RedeemCode();
    $total = $model->getRedeemMetaCount();
    $config = [
        "baseurl" => U('admin.php', ['c' => 'redeem_code',
            'a' => 'index',
        ]),
        'total' => $total,    //设置记录总数
        'pagesize' => C('PAGE_SIZE'),       //设置每页数量
        'current_page' => I('get.p/d', 1), //设置当前页码
    ];

    $pagination = new Lib\Pagination($config);//分页类
    $fw->smarty->assign("pagination_link", $pagination->createLink());

    $list = $model->getMetaList($pagination->start, $pagination->offset);
    $fw->smarty->assign('typeArr', $model->typeArr);
    $fw->smarty->assign('list', $list);
    $fw->smarty->display('redeem_code/index.html');
}

/**
 * @pageroute
 */
function status()
{
    $model = new  \Model\RedeemCode();

    $id = I('get.id/d', 0);
    if ($id) {
        if ($model->switchStausById($id)) {
            ajaxReturn(['error' => 200, 'msg' => '切换成功']);
        } else {
            ajaxReturn(['error' => 100, 'msg' => '切换失败']);
        }
    } else {
        ajaxReturn(['error' => 100, 'msg' => '数据不合法']);

    }
}

/**
 * @pageroute
 */
function detail()
{
    $fw = getFrameworkInstance();
    $model = new  \Model\RedeemCode();
    $id = I('id', 0, 'intval');
    $type = I('type', '', 'trim');
    $total = I('total', '', 'trim');
    $used = I('used', '', 'trim');
    $config = [
        "baseurl" => U('admin.php', ['c' => 'redeem_code',
            'a' => 'detail',
            'type' => $type,
            'id' => $id,
            'total' => $total,
            'used' => $used,
        ]),
        'total' => $total,    //设置记录总数
        'pagesize' => C('PAGE_SIZE'),       //设置每页数量
        'current_page' => I('get.p/d', 1), //设置当前页码
    ];

    $pagination = new Lib\Pagination($config);//分页类
    $list = $model->getListRedeemDtail($id, $pagination->start, $pagination->offset);
    $fw->smarty->assign("pagination_link", $pagination->createLink());
    $fw->smarty->assign('list', $list);
    $fw->smarty->assign('metaId', $id);
    $fw->smarty->assign('type', $type);
    $fw->smarty->assign('total', $total);
    $fw->smarty->assign('used', $used);
    $fw->smarty->display('redeem_code/detail.html');

}

/**
 * @pageroute
 */
function export()
{
    set_time_limit(0);
    $id = I('id', 0, 'intval');
    $fileName = "批次为————" . $id . ".csv";
    $now = gmdate("D, d M Y H:i:s");

    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");


    header("Content-Disposition: attachment;filename={$fileName}");
    header("Content-Transfer-Encoding: binary");

    $fp = fopen('php://output', 'a') or die('打开输出失败');

    $heads = ['ID', '兑换码', '兑换用户ID', '兑换时间', '类型', '状态'];
    foreach ($heads as $k => $v) {
        $heads[$k] = iconv('utf-8', 'gbk', $v);
    }

    fputcsv($fp, $heads);

    $limit = 10000;
    $count = 0;
    $model = new  \Model\RedeemCode();
    $isUsed = [0 => '未使用', 1 => '已使用'];
    foreach ($model->export($id) as $vv){
        $row = [];
        $row[] = $vv['id'];
        $row[] = $vv['code'];
        $row[] = $vv['user_id'];
        $row[] = $vv['redeem_time'];
        $row[] = iconv('utf-8', 'gbk', $model->typeArr[$vv['type']]);

        $row[] = iconv('utf-8', 'gbk', $isUsed[$vv['status']]);
        fputcsv($fp, $row);
        $count++;
        if ($count==$limit){
            ob_flush();
            flush();
            $count = 0;
        }

    }
    fclose($fp);
    die();
}

/**
 * @pageroute
 */
function search()
{
    $fw = getFrameworkInstance();
    $model = new  \Model\RedeemCode();
    $content = I('content', '', 'trim');

    $res = $model->getSearch($content);

    $fw->smarty->assign('list', $res['list']);
    $fw->smarty->assign('metaId', $res['metaId']);
    $fw->smarty->assign('type', $res['type']);
    $fw->smarty->assign('total', $res['total']);
    $fw->smarty->assign('used', $res['used']);
    $fw->smarty->display('redeem_code/detail.html');
}

/**
 * @pageroute
 */
function add()
{
    if (!IS_AJAX) {
        ajaxReturn(['message' => '非法访问']);
    }
    $data['name'] = I('post.redeem_name', '', 'trim');
    $data['type'] = I('post.prize_type', 0, 'intval');
    $data['map_id'] = I('post.prize_id', 0, 'intval');
    $data['total'] = I('post.prize_num', 0, 'intval');
    $data['user_max_get'] = I('post.max_user_num', 0, 'intval');
    $data['start_time'] = I('post.start_time', '', 'trim');
    $data['end_time'] = I('post.end_time', '', 'trim');

    $model = new  \Model\RedeemCode();

    if (!$model->verifyAble($data['type'], $data['map_id'])) {
        ajaxReturn(['error' => 100, 'msg' => '兑换物品不可用']);
    }

    $id = $model->addRedeem($data);
    if (!$id) {
        ajaxReturn(['error' => 100, 'msg' => '添加失败']);
    }
    $count = $model->generateCode($id, $data);
    if (!$count) {
        ajaxReturn(['error' => 100, 'msg' => '添加失败']);
    }

    ajaxReturn(['error' => 200, 'msg' => '生成 ' . $count . ' 个成功']);

}
