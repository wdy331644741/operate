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
    if ($total >= 0.01) {

        $fromUserId = I('post.userId', '', 'intval');

        $postParams = array(
            'user_id' => $fromUserId,
        );

        //获取邀请用户id
        $result = $message = Common::jsonRpcApiCall((object)$postParams, 'getFormUserId', config('RPC_API.passport'));
        $userId = (isset($result['result']['user_id']) && !empty($result['result']['user_id'])) ? $result['result']['user_id'] : '';

        if ($userId) {
            $data = [
                'user_id'               => $userId,
                'from_user_id'          => $fromUserId,
                'amount'                => $total,
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
