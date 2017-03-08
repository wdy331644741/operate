<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;

/**
 * 活动触发
 *
 * @pageroute
 **/
function trigger()
{
    $userId = I('post.user_id', '', 'intval');
    $type = I('post.node_name', '', 'strval');

    $nodeModel = new \Model\AwardNode();

    $nodeId = $nodeModel->getNode($type);
    try {
        if ($expId = havaUsefulExperience($nodeId)) {
            pushExperienceRecord($userId, $expId);
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

/**
 * 手动发放
 *
 * @pageroute
 **/
function manual()
{
    $manualPushRecordsModel = new \Model\AwardExtend();
    $unsendRecords = $manualPushRecordsModel->getUnsendRecords();
    if (!empty($unsendRecords)) {
        foreach ($unsendRecords as $record) {
            if ($nums = dealRecordAndReturnSuccessNums($record)) {
                $manualPushRecordsModel->updateSendStatus($record['id'], $nums);
            }
        }
    }
    echo $nums;
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

function havaUsefulInterestCoupon($nodeId)
{
    $interestCoupon = new \Model\AwardInterestcoupon();
    $awardInfo = $interestCoupon->filterUsefulInterestCoupon($nodeId);
    if (empty($awardInfo)) {
        return false;
    }

    return $awardInfo['id'];
}

//处理奖品发放记录并返回成功数
function dealRecordAndReturnSuccessNums($record)
{
    $successNums = 0;
    $userIds = filterAndMapPhoneToUserIds($record['user']);

    foreach ($userIds as $userId) {
        try {
            if ($record['award_type'] == 1) {
                pushExperienceRecord($userId, $record['award_id']);
            }

            if ($record['award_type'] == 2) {
                pushInterestCoupon($userId, $record['award_id']);
            }
        } catch (\Exception $e) {
            continue;
        }
        $successNums++;
    }

    return $successNums;
}

function filterAndMapPhoneToUserIds($user)
{
    $result = [];
    if (is_string($user)) {
        $user = explode(',', $user);
    }

    foreach ($user as $item) {
        if ($userId = ifUserExistsAndReturnUserId($item)) {
            $result[] = $userId;
        }
    }

    //去重，同样的用户只发一次
    return array_unique($result);
}

function ifUserExistsAndReturnUserId($userMark)
{
    $userModel = new \Model\AuthUser();
    if (preg_match('/^1[0-9]{10}$/', $userMark)) {
        $userInfo = $userModel->getUserInfoByName($userMark);
        if (!empty($userInfo)) {
            return $userInfo->id;
        }
    }

    if ($userModel->getUserName($userMark)) {
        return $userMark;
    }

    return false;
}

//发放体验金
function pushExperienceRecord($userId, $expId)
{
    $params = array('user_id' => $userId, 'id' => $expId);

    Common::localApiCall((object) $params, 'pushExperienceRecord', 'InsideRpcImpl');
}

//发放理财券
function pushInterestCoupon($userId, $couponId)
{
    $params = array('user_id' => $userId, 'id' => $couponId);

    Common::localApiCall((object) $params, 'pushInterestCoupon', 'InsideRpcImpl');
}

/**
 * 记录好友共享收益
 * @pageroute
 */
function friendsShare()
{
    $model = new \Model\MarketingRevenueSharing();

    logs('记录好友共享收益:' . PHP_EOL . var_export($_POST, true), 'friendsShare');

    $data = [
        'user_id'    => I('post.userId', '', 'intval'),
        'amount'     => I('post.total'),
        'start_time' => I('post.beginTime'),
        'end_time'   => I('post.endTime'),
    ];

    try {
        $model->addRevenueSharing($data, I('post.type', '', 'strval'));
    } catch (\Exception $e) {
        $msg = "接口错误码：{$e->getCode()}, 错误信息：{$e->getMessage()}" . PHP_EOL;
        logs($msg, 'friendsshare');

        echo $msg;
    }

}
