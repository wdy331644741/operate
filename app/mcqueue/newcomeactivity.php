<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;

/**
 * 新手活动触发
 * @pageroute
 */
function register(){
	$userId = I('post.user_id', '', 'intval');
	$time = I('post.time', '');
    // $type = I('post.node_name', '', 'strval');
    $type = 'register';
    $nodeModel = new \Model\AwardNode();

    $nodeId = $nodeModel->getNode($type);
	try {
        if ($expId = havaUsefulExperience($nodeId)) {
            pushActivateExperienceRecord($userId, $expId);
        }

        if ($couponId = havaUsefulInterestCoupon($nodeId)) {
            pushInterestCoupon($userId, $couponId);
        }
    } catch (\Exception $e) {
        $msg = "用户ID: {$userId} 触发：{$type}，发放入账失败：" . PHP_EOL;
        $msg .= "接口错误码：{$e->getCode()}, 错误信息：{$e->getMessage()}" . PHP_EOL;
        logs($msg, 'trigger');

        echo $msg;
    }
}



function havaUsefulExperience($nodeId)
{
    $experience = new \Model\AwardExperience();
    $awardInfo = $experience->filterUsefulExperience($nodeId);
    if (empty($awardInfo)) {
        return false;
    }

    return $awardInfo['id'];
}

function pushActivateExperienceRecord($userId, $expId)
{
    $params = array('user_id' => $userId, 'id' => $expId);
    echo $userId,"----",$expId;

}