<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 **/
function index()
{
    $node = 'signin';
    $data = array(
        'code' => '123456'
    );
    $params = array(
        'phone'     => '18801301379',
        'node_name' => $node,
        'tplParam'  => $data
    );
    \App\service\rpcserverimpl\Common::localApiCall((object) $params, "smsMessage", 'PushRpcImpl');
    die('success');
    echo date('Y-m-d', strtotime("-1 days"));

    die;
    \Lib\UserData::set('isset_tradepwd', '130429199104216215');
    echo \Lib\UserData::get('id_number');
}

/**
 * @pageroute
 **/
function sms()
{
    $smslog = new \Model\SmsLog();
    $list = $smslog->orderby('id desc')->get()->resultArr();

    $html = '<table border="1">';
    $html .= "<tr><td>手机号</td><td>短信内容</td><td>发送时间</td></tr>";
    foreach ($list as $item) {
        $html .= "<tr><td>{$item['mobile']}</td><td>{$item['contents']}</td><td>{$item['created_at']}</td></tr>";
    }
    $html .= '</table>';
    echo $html;
}

/**
 * @pageroute
 **/
function notify()
{
    $id = I('get.id', 0, 'intval');
    $type = I('get.type', '', 'strval');
    $status = I('get.status', 0, 'intval');

    if (empty($id) || empty($type)) {
        die('参数错误');
    }
    if ($type == 'w') {
        $model = new \Model\MarginWithdraw();
        $orderInfo = $model->getInfo($id);
        $res = $model->modifyWithdrawStatus($orderInfo['order_id'], $status);
    } else {
        $model = new \Model\MarginRecharge();
        $orderInfo = $model->getRecording($id);
        $res = $model->modifyRechargeStatus($orderInfo['order_id'], $status);
    }

    if ($res) {
        die('success');
    }

    die('失败');
}

