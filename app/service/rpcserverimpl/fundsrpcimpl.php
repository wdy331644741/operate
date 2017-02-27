<?php
/**
 * Author     : newiep
 * CreateTime : 2016-07-01 16:54
 * Description: 用户资金相关Rpc服务
 */

namespace App\service\rpcserverimpl;

use Lib\UserData;
use App\service\Traits\Validator;
use App\service\Traits\Signature;
use App\service\exception\AllErrorException;

class FundsRpcImpl extends BaseRpcImpl
{

    use Validator, Signature;

    const INFINITE = 'infinite';

    protected $withdrawUuid;

    protected $orderId;

    //充值是否需要短信
    protected $smsConfirm;

    //银行单日限额
    protected $dailyQuota;

    //银行卡单次限额
    protected $timesQuota;

    //剩余可购买份额
    protected $remainingAmount;

    /**
     * 资产流动限制信息
     *
     * @JsonRpcMethod
     */
    public function assetFlowsLimit()
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //检查是否绑卡
        if (!UserData::get('is_bindcard')) {
            throw new AllErrorException(AllErrorException::NOT_BIND_BANKCARD);
        }
        $withdraw = new \Model\MarginWithdraw();

        //银行卡剩余可支付
        $bankOverage = $this->getUserTodayRechargeLave($this->userId);

        //当月提现次数
        $freeNumber = (int) $withdraw->getFreeWithdrawNum($this->userId);

        $withdrawOverage = $this->getUserTodayWithdrawLave($this->userId);

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => array(
                'sell_overage'      => $this->remainingAmount,  //剩余份额
                'bank_overage'      => $bankOverage,                  //剩余可买入
                'free_withdraw_num' => $freeNumber,                   //免费提现次数
                'withdraw_overage'  => $withdrawOverage               //当日可转出
            )
        );
    }

    protected function getUserTodayRechargeLave($userId)
    {
        $recharge = new \Model\MarginRecharge();
        $personRechargeTotal = $recharge->currentTotalDealByUserId($userId);

        $cardInfo = $this->getBankCardQuota($userId);
        $this->remainingAmount = $this->getRemaining();

        if ($this->remainingAmount == \Model\ConfigPurchase::INFINITE) {
            $daysRechargeQuota = $cardInfo['days_quota'];
        } else {
            $daysRechargeQuota = $this->remainingAmount;
        }

        $daysLaveAmount = bcsub($daysRechargeQuota, $personRechargeTotal, 2);

        return bccomp($daysLaveAmount, 0) < 0 ? 0 : $daysLaveAmount;
    }

    protected function getUserTodayWithdrawLave($userId)
    {
        $withdraw = new \Model\MarginWithdraw();
        $configCapital = new \Model\ConfigCapital();
        $withdrawTotal = $withdraw->getDailyWithdrawAmount($userId, date('Y-m-d H:i:s'));

        $config = $configCapital->formatAllSettings();
        $daysLaveAmount = bcsub($config['user_withdraw_day_amount'], $withdrawTotal);

        return $daysLaveAmount < 0 ? 0 : $daysLaveAmount;
    }


    protected function getRemaining()
    {
        $purchase = new \Model\ConfigPurchase();
        $purchaseInfo = $purchase->getLatestSetting();

        return $purchaseInfo['status'] == $purchase::STATUS_INFINITE ? $purchase::INFINITE : $purchaseInfo['purchase_amount'];
    }

    protected function getBankCardQuota($userId)
    {
        //获取用户银行卡限额
        $authBank = new \Model\AuthBankCard();
        $bindCard = $authBank->getBindCard($userId);

        return $this->getCardInfo($userId, $bindCard['cardno']);
    }

    /**
     * 充值（根据通道确定发送验证码）
     *
     * @JsonRpcMethod
     */
    public function recharge($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        if (!$this->checkedBindInfo($params->cardno)) {
            throw new AllErrorException(AllErrorException::NOT_BIND_BANKCARD);
        }

        if (!$this->compareRechargeQuota($params->amount)) {
            throw new AllErrorException(AllErrorException::VALID_NUMERICAL_FAIL, [], '充值金额超出限额范围');
        }

        //如果不需要发送验证码，则需要交易密码验证，然后直接充值
        if (!$this->ifNeedSmsConfirm()) {
            Common::localApiCall($params, 'checkTradePwd', 'SecureRpcImpl');
        }

        return $this->storeOrderAndCallGateway($params->amount);
    }

    /**
     * 确认充值
     *
     * @JsonRpcMethod
     */
    public function confirmRecharge($params)
    {

    }

    /**
     * 支付结果异步通知地址
     *
     * @JsonRpcMethod
     */
    public function rechargeNotify($params)
    {
        logs($params, "notify");

        if (!isset($params->orderid) || !isset($params->sign)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        //简单身份验证
        if ($params->sign != hash('sha256', $params->orderid . ACCOUNT_SECRET)) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL);
        }

        //错误信息
        $errorMsg = empty($params->errormsg) ? '' : $params->errormsg;

        //修改充值订单状态
        $rechargeModel = new \Model\MarginRecharge();
        $result = $rechargeModel->modifyRechargeStatus($params->orderid, $params->status, $errorMsg);

        if ($result) {
            return array(
                'code'    => 0,
                'message' => 'success'
            );
        }

        throw new AllErrorException(AllErrorException::SAVE_CHARGE_FAIL);
    }

    /**
     * 查询充值结果
     *
     * @JsonRpcMethod
     *
     * @throws AllErrorException
     */
    public function inquireRecharge($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //接口必要参数
        if (!isset($params->order_id)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '缺少订单号');
        }
        //唯一查询编号
        $request_id = generate_orderid();

        $result = Common::jsonRpcApiCall([$params->order_id, $request_id], 'orderQuery', config('RPC_API.pay'), false);

        //处理银行接口数据，并返回状态信息
        return OrderStatus::getRechargeStatus($result, $this->userId, $params->order_id);

    }

    /**
     * 获取recharge orderId
     */
    protected function getOrderId()
    {
        $this->orderId = $this->orderId ?: generate_orderid(PREFIX_RECHARGE_ID);

        return $this->orderId;
    }

    /**
     * 充值是否需要发送短信
     * @return mixed
     */
    protected function ifNeedSmsConfirm()
    {
        return !empty($this->smsConfirm) && $this->smsConfirm;
    }

    /**
     * 检查用户买入限制
     *
     * @param $money
     * @return bool
     * @throws AllErrorException
     */
    protected function compareRechargeQuota($money)
    {
        if (empty($money) || !$this->validateMoney($money)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '充值金额不合法');
        }

        if (bccomp($money, BOTTOM_QUOTA) == -1) {
            throw new AllErrorException(
                AllErrorException::API_MIS_PARAMS, [], '最低充值' . BOTTOM_QUOTA . '元'
            );
        }

//        $recharge = new \Model\MarginRecharge();

        return true;
    }

    /**
     * 检查充值订单是否有效
     *
     * @param $orderId
     * @return bool
     */
    protected function checkOrderIsAvailable($orderId)
    {
        $rechargeModel = new \Model\MarginRecharge();

        //查询充值记录
        $rechargeInfo = $rechargeModel
            ->where("`order_id` = '{$orderId}' AND `status` = 100")
            ->get()->rowArr();
        if (empty($rechargeInfo)) {
            return false;
        }

        return true;
    }


    /**
     * 保存订单并调用网关支付接口
     *
     * @param $amount
     * @return array
     * @throws AllErrorException
     */
    protected function storeOrderAndCallGateway($amount)
    {
        $recharge = new \Model\MarginRecharge();
        $orderId = $this->getOrderId();
        $rechargeId = $recharge->addRecording($this->userId, $orderId, $amount);

        if (!$rechargeId) {
            throw new AllErrorException(AllErrorException::SAVE_CHARGE_FAIL);
        }

        if ($this->ifNeedSmsConfirm()) {
            return $this->callGateWaySendCode($rechargeId);
        }

        return $this->callGateWayToPay($rechargeId);
    }

    /**
     *
     * @param $id
     * @return array
     * @throws AllErrorException
     */
    protected function callGateWaySendCode($id)
    {
        $recharge = new \Model\MarginRecharge();
        $recording = $recharge->getRecording($id);

        $params = array(
            'orderid'     => $recording['order_id'],
            'amount'      => bcmul($recording['amount'], 100),
            'identityid'  => $recording['user_id'],
            'bankaccount' => $recording['bank_account'],
            'username'    => $recording['realname'],
            'phone'       => $recording['phone'],
            'userip'      => $recording['client_ip'],
            'productname' => "活期买入",
            'productdesc' => "活期买入{$recording['amount']}元"
        );

        $response = Common::jsonRpcApiCall(
            $params, 'prePay', config('RPC_API.pay'), false
        );

        if (!isset($response['result'])) {
            throw new AllErrorException(AllErrorException::API_SMS);
        }

        return array(
            'code'     => 0,
            'message'  => 'success',
            'order_id' => $recording['order_id']
        );
    }

    /**
     * 调用网关支付接口
     *
     * @param $id
     * @return array
     */
    protected function callGateWayToPay($id)
    {
        $recharge = new \Model\MarginRecharge();
        $recording = $recharge->getRecording($id);

        $params = array(
            'orderid'     => $recording['order_id'],
            'amount'      => bcmul($recording['amount'], 100),
            'identityid'  => $recording['user_id'],
            'bankaccount' => $recording['bank_account'],
            'card_top'    => substr($recording['bank_account'], 0, 6),
            'card_top'    => substr($recording['bank_account'], -4),
            'userip'      => $recording['client_ip'],
            'productname' => "活期买入",
            'productdesc' => "活期买入 {$recording['amount']} 元",
            'transtime'   => time()
        );

        $response = Common::jsonRpcApiCall(
            (object) $params, 'directBindPay', config('RPC_API.pay'), false
        );

        return OrderStatus::getRechargeStatus($response, $this->userId, $recording['order_id']);
    }

    /**
     * 提现
     *
     * @JsonRpcMethod
     */
    public function withdrawFee($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //检查提现金额
        if (empty($params->amount) || !$this->validateMoney($params->amount)) {
            throw new AllErrorException(
                AllErrorException::VALID_WITHDRAW_FAIL, [], '转出金额非法'
            );
        }
        $marginModel = new \Model\MarginMargin();

        //检查实际到账
        $feeData = $marginModel->getWithdrawFee($this->userId, $params->amount);
        if (!isset($feeData['actual_amount']) || bccomp($feeData['actual_amount'], 0.00, 2) < 1) {
            throw new AllErrorException(
                AllErrorException::VALID_WITHDRAW_FAIL, [], '扣除提现费用后，实际到账0元，无法提现'
            );
        }

        return array(
            'code'    => 0,
            'message' => "success",
            'data'    => array(
                'total_fee'     => $feeData['total_fee'],
                'actual_amount' => $feeData['actual_amount']
            )
        );
    }

    /**
     * 提现操作
     *
     * @JsonRpcMethod
     */
    public function withdraw($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        if (empty($params->trade_pwd)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '交易密码不为空');
        }

        if (empty($params->amount) || bccomp($params->amount, MIN_WITHDRAD_QUOTA) == -1) {
            throw new AllErrorException(
                AllErrorException::API_MIS_PARAMS, [], '提现金额最少为' . MIN_WITHDRAD_QUOTA . '元'
            );
        }

        if (!$this->checkedBindInfo($params->cardno)) {
            throw new AllErrorException(AllErrorException::NOT_BIND_BANKCARD);
        }

        //检查账户是否冻结
        $authUser = new \Model\AuthUser();
        $userInfo = $authUser->getUserBasicInfo($this->userId);
        if ($userInfo->is_active == 0) {
            throw new AllErrorException(AllErrorException::VALID_CAPTCHA_FAIL, [], '账号已冻结');
        }

        //调用rpc接口，检查交易密码是否正确
        Common::localApiCall($params, 'checkTradePwd', 'SecureRpcImpl');

        if ($this->checkWithdrawMoneyAndRecord($params->amount)) {

            return array(
                'code'    => 0,
                'message' => "资产转出申请成功"
            );
        }

        throw new AllErrorException(AllErrorException::SAVE_WITHDRAE_FAIL);
    }

    /**
     * 提现结果异步通知地址
     *
     * @JsonRpcMethod
     */
    public function withdrawNotify($params)
    {
        logs($params, "notify");
        if (!isset($params->orderid) || !isset($params->sign)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        //简单身份验证
        if ($params->sign != hash('sha256', $params->orderid . ACCOUNT_SECRET)) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL);
        }

        //错误信息
        $errorMsg = empty($params->errormsg) ? '' : $params->errormsg;

        //修改充值订单状态
        $rechargeModel = new \Model\MarginWithdraw();
        if (
            $params->status == 1 ||
            $params->status == 0
        ) {
            $result = $rechargeModel->modifyWithdrawStatus($params->orderid, $params->status, $errorMsg);

            if ($result) {
                return array(
                    'code'    => 0,
                    'message' => 'success'
                );
            }

            throw new AllErrorException(AllErrorException::SAVE_WITHDRAE_FAIL);
        }

    }

    /**
     * 人工审核接口
     * @JsonRpcMethod
     *
     * @param $params
     * @return array
     * @throws AllErrorException
     */
    public function manualAudit($params)
    {
        if (!isset($params->withdrawId) || !isset($params->sign)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        //简单身份验证
        if ($params->sign != hash('sha256', $params->withdrawId . ACCOUNT_SECRET)) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL);
        }

        $res = $this->payUserAndUpdateAuditMode($params->withdrawId, true);
        if ($res) {
            return array(
                'code'    => 0,
                'message' => 'success'
            );
        }
        throw new AllErrorException(AllErrorException::SAVE_WITHDRAE_FAIL);
    }

    /**
     * 提现申请拒绝
     * @JsonRpcMethod
     */
    public function refuseAudit($params)
    {
        if (!isset($params->withdrawId) || !isset($params->sign)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        //简单身份验证
        if ($params->sign != hash('sha256', $params->withdrawId . ACCOUNT_SECRET)) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL);
        }

        $withdraw = new \Model\MarginWithdraw();
        if ($withdraw->auditRefuse($params->withdrawId, $params->errorMsg)) {
            return array(
                'code'    => 0,
                'message' => '审核操作成功'
            );
        }
        throw new AllErrorException(AllErrorException::SAVE_WITHDRAE_FAIL);
    }

    /**
     * 查询提现结果
     *
     * @JsonRpcMethod
     *
     * @throws AllErrorException
     */
    public function inquireWithdraw($params)
    {
        //接口必要参数
        if (!isset($params->order_id)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '缺少订单号');
        }

        $result = Common::jsonRpcApiCall([$params->order_id], 'withdrawQuery', config('RPC_API.pay'), false);

        if (isset($result['result']['status'])) {
            $withdraw = new \Model\MarginWithdraw();
            if (
                $result['result']['status'] == 1 ||
                $result['result']['status'] == 0
            ) {
                $withdraw->modifyWithdrawStatus($params->order_id, $result['result']['status']);

                return array(
                    'code'    => 0,
                    'message' => 'success',
                    'status'  => $result['result']['status']
                );
            }
        }
    }

    //用户是否有提现未处理
    protected function haveProcessingWithdraw($userId)
    {
        $marginModel = new \Model\MarginMargin();
        $marginInfo = $marginModel->getMarginByUserId($userId);

        return bccomp($marginInfo['withdrawing_amount'], 0) == 1;
    }

    /**
     * 提现金额和当前可转出资产做比较并插入提现记录
     * @param $money
     * @return bool
     * @throws AllErrorException
     */
    protected function checkWithdrawMoneyAndRecord($money)
    {
        //1、获取用户当前可用余额
        //2、通知并记录用户当前收益
        //3、提现金额和当前账户余额做比较
        //4、判断资金池，决定审核方式（自动或人工）并插入提现记录
        //5、通知计算收益端，提现审核（100）
        //6、若自动，则调用支付网关，支付用户提现金额，并通知计算收益端（审核完成200 或失败400），返回
        //7、若人工审核，则等待人工点击审核按钮或资金池充裕后，调用支付网关，并通知计算收益端（审核完成200 或失败400）

        if (!$this->compareWithdrawMoneyToAvailable($money)) {
            throw new AllErrorException(AllErrorException::VALID_WITHDRAW_FAIL);
        }

        return $this->decideAuditModeAndStore($money);
    }

    /**
     * 提现金额和可转出金额做比较
     * @param $money
     * @return bool
     */
    protected function compareWithdrawMoneyToAvailable($money)
    {
        if (!$this->compareWithdrawQuota($money)) {
            return false;
        }

        if ($this->compareAvailable($money)) {
            return true;
        }

        //流水记录失败，则尝试同步根据提现唯一标示
        if (!$this->calculateCurrentProfit($money)) {
            //两个方法调用相同接口，注释
//            $this->syncProfitByUuidAndRecord($money);
        }

        //计算当前结息后并更新资产后，重新进行金额比较
        return $this->compareAvailable($money);
    }

    /**
     * 决定审核模式（自动、人工）并插入提现记录
     *
     * @param $money
     * @return bool
     * @throws AllErrorException
     */
    protected function decideAuditModeAndStore($money)
    {
        $withdraw = new \Model\MarginWithdraw();
        $uuid = $this->getWithdrawUuid();

        $withdrawId = $withdraw->addRecording($this->userId, $uuid, $money);
        if (!$withdrawId) {
            throw new AllErrorException(
                AllErrorException::VALID_WITHDRAW_FAIL, [], '添加转出记录失败'
            );
        }
        //资金池是否充裕，满足自动审核
        if ($this->isAutoAuditMode($money)) {
            return $this->payUserAndUpdateAuditMode($withdrawId);
        }

        return true;
    }

    /**
     * 调用结息端获取当前时间收益流水并更新可转出资产
     *
     * @param $money
     * @return bool
     */
    protected function calculateCurrentProfit($money)
    {
        $params = array(
            'user_id'     => $this->userId,
            'user_name'   => UserData::get('user_name'),
            'user_mobile' => UserData::get('phone'),
//            'token'       => $this->getWithdrawUuid(),
            'token'       => create_guid(),
            'amount'      => $money
        );
        //通知记录流水
        $response = Common::jsonRpcApiCall(
            (object) $params, 'giveUserEarnings', config('RPC_API.projects'), false
        );

        //记录当前时间点利息
        if (isset($response['result']['code']) && $response['result']['code'] == 0) {
            return Common::localApiCall(
                (object) $response['result']['data'], 'refund', 'TradeRpcImpl'
            );
        }

        return false;
    }

    /**
     * 根据withdraw uuid 同步当前结息流水并记录账户余额
     *
     * @param $money
     * @return void
     * @throws AllErrorException
     */
    protected function syncProfitByUuidAndRecord($money)
    {
        //同步利息接口
        $params = array(
            'user_id'     => $this->userId,
            'user_name'   => UserData::get('user_name'),
            'user_mobile' => UserData::get('phone'),
//            'token'       => $this->getWithdrawUuid(),
            'token'       => create_guid(),
            'amount'      => $money
        );
        $response = Common::jsonRpcApiCall(
            (object) $params, 'giveUserEarnings', config('RPC_API.projects'), false
        );

        if (!isset($response['result']['code']) || $response['result']['code'] != 0) {
            throw new AllErrorException(AllErrorException::VALID_WITHDRAW_FAIL);
        }

        //添加结息流水并更新账户可转出资产
        Common::localApiCall(
            $response['result']['data'], 'refund', 'TradeRpcImpl'
        );
    }

    /**
     * 调用支付通道代付接口，并修改审核模式
     * @param $withdrawId
     * @param $isManual
     * @return bool
     */
    protected function payUserAndUpdateAuditMode($withdrawId, $isManual = false)
    {
        $withdraw = new \Model\MarginWithdraw();
        $withdrawRecording = $withdraw->getInfo($withdrawId);

        //调用支付网关代付接口
        if ($this->payUserByChannel($withdrawRecording)) {
            $withdraw->auditComplete($withdrawId, $isManual);

            //发送消息通知
            Common::messageBroadcast('withdraw', array(
                'user_id'     => $this->userId,
                'withdraw_id' => $withdrawId,
                'type'        => 'withdraw_audit',
                'datetime'    => date("m月d日H时i分")
            ));
        }

        return true;
    }

    /**
     * 调用代付网关
     *
     * @param $recording
     * @return bool
     * @throws AllErrorException
     */
    protected function payUserByChannel($recording)
    {
        if (empty($recording)) {
            throw new AllErrorException(
                AllErrorException::API_ILLEGAL, [], '请求非法，转出记录不存在'
            );
        }
        $params = array(
            'orderid'    => $recording['order_id'],
            'username'   => $recording['realname'],
            'transtime'  => $recording['update_time'],
            'amount'     => bcmul($recording['amount'], 100),
            'bankname'   => $recording['bank_name'],
            'bankcode'   => $recording['bank_code'],
            'purpose'    => '活期资产转出',
            'cardno'     => $recording['bank_account'],
            'identityid' => $recording['user_id'],
            'userip'     => $recording['client_ip']
        );
        $response = Common::jsonRpcApiCall(
            (object) $params, 'withdraw', config("RPC_API.pay"), false
        );
        if (isset($response['result']['resp_code']) && $response['result']['resp_code'] == 1) {
            return true;
        }
        Common::debugTrace($params, 'withdraw', $response);

        return false;
    }


    /**
     * 和用户账户可转出金额做比较
     *
     * @param $money
     * @return bool
     */
    protected function compareAvailable($money)
    {
        $margin = new \Model\MarginMargin();
        $marginInfo = $margin->getMarginByUserId($this->userId);

        return bccomp($money, $marginInfo['avaliable_amount'], 2) <= 0;
    }

    /**
     * 检查用户转出限制
     *
     * @param $money
     * @return bool
     * @throws AllErrorException
     */
    protected function compareWithdrawQuota($money)
    {
        if (empty($money) || !$this->validateMoney($money)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '提现金额不合法');
        }

        return true;
    }

    /**
     * 获取withdraw uuid
     */
    protected function getWithdrawUuid()
    {
        $this->withdrawUuid = $this->withdrawUuid ?: create_guid();

        return $this->withdrawUuid;
    }

    /**
     * 检查是否绑定银行卡
     *
     * @param $cardNo
     * @return bool
     * @throws AllErrorException
     */
    protected function checkedBindInfo($cardNo)
    {
        //接口必要参数
        if (empty($cardNo)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '银行卡号不为空');
        }

        //检查是否绑卡
        $bankCardModel = new \Model\AuthBankCard();
        $bankCardInfo = $bankCardModel->getBindCard($this->userId, $cardNo);
        if (empty($bankCardInfo)) {
            return false;
        }

        $cardInfo = $this->getCardInfo($this->userId, $cardNo);
        if ($cardInfo['isvalid'] != 1) {
            throw new AllErrorException(AllErrorException::CARD_NOT_SUPPORT);
        }
        if ($cardInfo['isbind'] == 0) {
            throw new AllErrorException(AllErrorException::BIND_BANKCARD_AGAIN);
        }

        $this->smsConfirm = $cardInfo['smsconfirm'];

        $this->dailyQuota = $cardInfo['days_quota'];
        $this->timesQuota = $cardInfo['times_quota'];

        return true;
    }

    /**
     * 根据资金池剩余判断是否自动审核
     * @param $money
     * @return bool
     */
    protected function isAutoAuditMode($money)
    {
        //关闭自动审核
        if (config('OFF_AUTO_AUDIT', true)) {
            return false;
        }
        //邀请用户大于配置，不能自动提现
        $authUser = new \Model\AuthUser();
        $inviteCounts = $authUser->getInviteUserCounts($this->userId);
        if (bccomp($inviteCounts, config('AWARD_INVITE.audit_limit', 10)) == 1) {
            return false;
        }

        $capitalPool = 1000000;
        $res = (bccomp($money, $capitalPool, 2) <= 0);

        return $res;
    }
}
