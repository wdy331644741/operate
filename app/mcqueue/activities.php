<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;

/**
 * 记录好友共享收益
 * @pageroute
 */
function friendsShare()
{
    $model = new \Model\MarketingRevenueSharing();

    logs('记录好友共享收益:' . PHP_EOL . var_export($_POST, true), 'friendsShare');
    if(I('post.total') > 0.01){

        $total = I('post.total') * 0.10;
        $cashTotal = I('post.uesCashTotal') * 0.10;
        $interestCouponTotal = I('post.uesInterestCouponTotal') * 0.10;

        $marketingRevenueSharing = new \Model\MarketingRevenueSharing();
        $earnings = new \Model\ConfigEarnings();
        $configEarningsData = $earnings->getInfoByTitle('revenueSharing');
        $maxAmount = $configEarningsData['amount'];  //配置中的收益最大金额

        $start_time = $configEarningsData['start_time'];
        $end_time = $configEarningsData['end_time'];

        $headCount = $configEarningsData['head_count'];

        if (($total >= 0.0000000000) && (date("Y-m-d H:is", strtotime('-1 day')) < $end_time)) {

            $userId = I('post.userId', '', 'intval');

            $postParams = array(
                'user_id' => $userId,
            );

            //获取邀请用户id
            $result = $message = Common::jsonRpcApiCall((object)$postParams, 'getFormUserId', config('RPC_API.passport'));
            $fromUserId = (isset($result['result']['user_id']) && !empty($result['result']['user_id'])) ? $result['result']['user_id'] : '';

            $sumamount = $marketingRevenueSharing->getSumByUserId($fromUserId);

            $finallyAmount = ($sumamount + $total) < $maxAmount ? $total : ($maxAmount - $sumamount);

            if ($finallyAmount > 0 && !empty($fromUserId)) {

                if ($finallyAmount != $total) {
                    $cashTotal = $cashTotal * ($finallyAmount / $total);
                    $interestCouponTotal = $interestCouponTotal * ($finallyAmount / $total);
                }
                $accountCountUserId = $model->getAccountCount($fromUserId, $start_time, $end_time);

                logs('accountCount:' . $accountCountUserId, 'accountCount');

                if (($userId && (intval(count($accountCountUserId)) < intval($headCount))) || in_array($userId, array_column($accountCountUserId, 'user_id'))) {

                    $getActivityUser = [
                        'user_id'    => $userId,
                        'start_time' => $start_time,
                        'end_time'   => $end_time
                    ];
                    $activityUser = Common::jsonRpcApiCall((object)$getActivityUser, 'getActivityUser', config('RPC_API.passport'));

                    if (isset($activityUser['result']['data']) && $activityUser['result']['data'] != false) {
                        $data = [
                            'user_id'               => $userId,
                            'from_user_id'          => $fromUserId,
                            'amount'                => $finallyAmount,
                            'cash_total'            => $cashTotal,
                            'interest_coupon_total' => $interestCouponTotal,
                            'start_time'            => I('post.beginTime'),
                            'end_time'              => I('post.endTime'),
                        ];

                        try {
                            $model->addRevenueSharing($data, I('post.type', '', 'strval'));
                        } catch (\Exception $e) {
                            $msg = "接口错误码：{$e->getCode()}, 错误信息：{$e->getMessage()}" . PHP_EOL;
                            logs($msg, 'friendsshare');

                            echo $msg;
                        }
                    }
                }
            }
        }
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

//处理奖品发放记录并返回成功数
function dealRecordAndReturnSuccessNums($record)
{
    $successNums = 0;
    $userIds = filterAndMapPhoneToUserIds($record['user']);

    foreach ($userIds as $userId) {
        try {
            if ($record['award_type'] == 1) {
                pushActivateExperienceRecord($userId, $record['award_id']);
            }

            if ($record['award_type'] == 2) {
                pushToBeActivatedInterestCoupon($userId, $record['award_id']);
            }
        } catch (\Exception $e) {
            continue;
        }
        $successNums++;
    }

    return $successNums;
}

//立即发放激活态体验金
function pushActivateExperienceRecord($userId, $expId)
{
    $params = array('user_id' => $userId, 'id' => $expId);

    // Common::localApiCall((object) $params, 'pushActivateExperienceRecord', 'InsideRpcImpl');
    Common::jsonRpcApiCall((object)$params, 'pushActivateExperienceRecord', config('RPC_API.passport'));
}


//发放理财券
function pushToBeActivatedInterestCoupon($userId, $couponId)
{
    $params = array('user_id' => $userId, 'id' => $couponId);

    // Common::localApiCall((object) $params, 'pushToBeActivatedInterestCoupon', 'InsideRpcImpl');
    Common::jsonRpcApiCall((object)$params, 'pushToBeActivatedInterestCoupon', config('RPC_API.passport'));
}