<?php


namespace App\service\rpcserverimpl;

use App\service\Traits\Signature;
use App\service\exception\AllErrorException;

class InsideRpcImpl extends BaseRpcImpl {

    use Signature;

    /**
     * 推送体验金记录
     *
     * @JsonRpcMethod
     */
    public function pushExperienceRecord($params)
    {
        $authUser = new \Model\AuthUser($params->user_id);
        $awardExp = new \Model\AwardExperience();
        $experience = new \Model\MarketingExperience();

        //1 获取体验金信息
        $experienceInfo = $awardExp->getDetail($params->id);

        //2 添加记录
        $experienceRecord = $experience->addExperienceForUser($authUser->id, $experienceInfo);

        //3 调用加息接口
        $params = array(
            'user_id'           => $authUser->id,
            'user_name'         => $authUser->username,
            'user_mobile'       => $authUser->phone,
            'token'             => $experienceRecord['uuid'],
            'experience_id'     => $experienceRecord['id'],
            'experience_period' => $experienceRecord['continuous_days'],
            'experience_amount' => $experienceRecord['amount']
        );
        Common::jsonRpcApiCall((object) $params, 'experienceBuy', config('RPC_API.projects'));
        //修改体验金状态
        $experience->updateStatusOfUse($experienceRecord['id']);

        return array(
            'code'    => 0,
            'message' => 'success'
        );
    }

    /**
     * 推送加息券记录
     *
     * @JsonRpcMethod
     */
    public function pushInterestCoupon($params)
    {
        $authUser = new \Model\AuthUser($params->user_id);
        $awardExp = new \Model\AwardInterestcoupon();
        $coupon = new \Model\MarketingInterestcoupon();

        //1 获取加息券信息
        $awardInfo = $awardExp->getDetail($params->id);

        //2 添加记录
        $couponRecord = $coupon->addCouponForUser($authUser->id, $awardInfo);

//        取消自动使用加息券
//        //3 调用加息接口
//        $params = array(
//            'user_id'               => $authUser->id,
//            'user_name'             => $authUser->username,
//            'user_mobile'           => $authUser->phone,
//            'token'                 => $couponRecord['uuid'],
//            'interestcoupon_id'     => $couponRecord['id'],
//            'interestcoupon_period' => $couponRecord['continuous_days'],
//            'interestcoupon_rate'   => bcmul($couponRecord['rate'], 100, 2)
//        );
//        Common::jsonRpcApiCall((object) $params, 'interestcouponBuy', config('RPC_API.projects'));
//        //修改加息券状态
//        $coupon->updateStatusOfUse($couponRecord['id']);

        return array(
            'code'    => 0,
            'message' => 'success'
        );
    }

    /**
     * 系统配置
     *
     * @JsonRpcMethod
     */
    public function getQuotaConfig()
    {
        if (!$this->checkedIpList()) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL, [], 'IP不合法');
        }

        $purchase = new \Model\ConfigPurchase();
        $configCapital = new \Model\ConfigCapital();
        $purchaseInfo = $purchase->getLatestSetting();
        $config = $configCapital->formatAllSettings();

        $isOpenPurchase = $purchaseInfo['status'] == $purchase::STATUS_INFINITE;

        $config = array(
            'isOpenPurchase'      => $isOpenPurchase,     //是否开放购买
            'purchaseQuota'       => $purchaseInfo['amount'],  //最大购买的限额
            'platformThrottle'    => $config['platform_threshold'],      //平台阀值
            'personPurchaseQuota' => $config['user_purchase_amount'],    //用户可购买最大额度
            'timesWithdrawQuota'  => $config['user_single_withdraw_amount'],      //用户单笔提现额度
            'dailyWithdrawQuota'  => $config['user_withdraw_day_amount'],      //用户单日提现额度
            'dailyWithdrawTotal'  => $config['withdraw_day_amount']      //通道每日可用提现金额
        );

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $config
        );
    }

    /**
     * 订单状态同步
     *
     * @JsonRpcMethod
     */
    public function syncOrderStatus($params)
    {
        if (!isset($params->uuid) || !isset($params->sign)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        //简单身份验证
        if ($params->sign != hash('sha256', $params->uuid . ACCOUNT_SECRET)) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL);
        }

        //查询订单状态
        $marginRecharge = new \Model\MarginRecharge();
        $orderInfo = $marginRecharge->getRecordingByUuid($params->uuid);

        //订单状态只能为处理中
        if (empty($orderInfo) || $orderInfo['status'] != '100') {
            throw new AllErrorException(
                AllErrorException::API_ILLEGAL, [], '订单状态不合法'
            );
        }

        //唯一查询编号
        $request_id = generate_orderid();

        //查询订单状态(APP)
        $result = Common::jsonRpcApiCall(
            [$orderInfo['order_id'], $request_id], 'orderQuery', config('RPC_API.pay'), false
        );

        //处理银行接口数据，并返回状态信息
        return OrderStatus::getRechargeStatus($result, $orderInfo['user_id'], $orderInfo['order_id']);
    }

    /**
     * 充值状态错误，补充值订单
     *
     * @JsonRpcMethod
     */
    public function errorRecharge($params)
    {
        if (!$this->validSignature($params)) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL);
        }

        //修改充值订单状态
        $errorBillModel = new \Model\MarginErrorBill();

        $result = $errorBillModel->refundRecharge($uid, $params->remark);

        if ($result) {
            return array(
                'code' => 0,
                'message' => 'success'
            );
        }

        throw new AllErrorException(
            AllErrorException::SAVE_WITHDRAE_FAIL, [], '坏账记录失败'
        );
    }

    /**
     * 转出审核
     *
     * @JsonRpcMethod
     */
    public function wihtdrawAudit($params)
    {
        if (!$this->validSignature($params)) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL);
        }


    }

    public function callInsideApi($method, $params)
    {
        if (method_exists($this, $method)) {
            //加签名
            $params = $this->createSignature($params);

            return $this->{$method}($params);
        }

        return false;
    }
}
