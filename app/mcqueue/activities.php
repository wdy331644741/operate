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
