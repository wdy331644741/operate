<?php
namespace Model;

use App\service\rpcserverimpl\Common;

class MarginRecharge extends Model
{

    const STATUS_ING = 100;
    const STATUS_SUCCESS = 200;
    const STATUS_FAIL = 400;

    protected $orderInfo;

    protected $rechargeResult;

    protected $errorMessage;

    public function __construct($pkVal = '')
    {
        parent::__construct('margin_recharge');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    //获取当前交易总额
    public function currentTotalSell($startedTime)
    {
        $result = $this->fields("SUM(amount) as amount", false)
            ->where("`create_time` > '{$startedTime}' and (`status` = 100 or `status` = 200)")
            ->get()->rowArr();

        if (empty($result)) {
            return 0;
        }

        return $result['amount'];
    }

    //获取当前交易总额
    public function currentTotalDealByUserId($userId, $startedTime = null)
    {
        if (empty($startedTime)) {
            $startedTime = date("Y-m-d 00:00:00");
        }

        $result = $this->fields("SUM(amount) as amount", false)
            ->where("`user_id` = {$userId} and `create_time` > '{$startedTime}' and (`status` = 100 or `status` = 200)")
            ->get()->rowArr();

        if (empty($result)) {
            return 0;
        }

        return $result['amount'];
    }

    //买入交易记录
    public function getUserRecords($userId)
    {
        return $this->fields("id, uuid, '1' as 'type', amount, bank_account, bank_name, update_time as datetime, status", false)
            ->where("`user_id` = {$userId}")
            ->get()->resultArr();
    }

    public function getRecording($id)
    {
        return $this->where("id = {$id}")->get()->rowArr();
    }

    public function getRecordingByOrderId($orderId)
    {
        return $this->where("`order_id` = '{$orderId}'")->get()->rowArr();
    }

    public function getRecordingByUuid($uuid)
    {
        return $this->where("`uuid` = '{$uuid}'")->get()->rowArr();
    }

    public function addRecording($userId, $orderId, $amount)
    {
        $authUserModel = new AuthUser($userId);
        $bankCardModel = new AuthBankCard();
        $marginModel = new MarginMargin();

        //身份信息和银行卡数据
        $bankCardInfo = $bankCardModel->getBindCard($userId);
        $marginInfo = $marginModel->getMarginByUserId($userId);

        //商户支付请求
        $rechargeData = array();//充值记录数据

        //记录充值订单
        $rechargeData['uuid'] = create_guid();
        $rechargeData['order_id'] = $orderId;
        $rechargeData['user_id'] = $userId;
        $rechargeData['realname'] = $authUserModel->realname;
        $rechargeData['id_number'] = $authUserModel->id_number;
        $rechargeData['phone'] = $authUserModel->phone;
        $rechargeData['amount'] = $amount;
        $rechargeData['pay_channel'] = 'UNSPAY';
        $rechargeData['bank_account'] = $bankCardInfo['cardno'];
        $rechargeData['bank_name'] = $bankCardInfo['bankname'];
        $rechargeData['from_platform'] = getUAInfo('platform');
        $rechargeData['client_ip'] = get_client_ip();
        $rechargeData['create_time'] = date("Y-m-d H:i:s");
        $rechargeData['update_time'] = date("Y-m-d H:i:s");

        //开启事务
        $this->transStart();

        try {
            $res = $this->add($rechargeData);
            if (!$res) {
                throw new \Exception('添加充值记录失败');
            }

            $marginRecData = array(
                'before_avaliable_amount' => $marginInfo['avaliable_amount'],
                'after_avaliable_amount'  => $marginInfo['avaliable_amount']
            );
            $remark = "于 " . date("Y-m-d H:i:s") . " 提交充值申请.";
            $recordRes = MarginRecord::record(
                $userId,
                $rechargeData['uuid'],
                $res,
                'recharge_request',
                $amount,
                $marginRecData,
                $remark,
                MarginRecord::NOT_AFFECTED_AVALIABLE
            );
            if (!$recordRes) {
                throw new \Exception('资产流水记录失败');
            }

            //提交事务
            $transStatus = $this->transCommit();
            if (!$transStatus) {
                throw new \Exception('事务提交失败');
            }

            return $res;

        } catch (\Exception $e) {
            logs($e->getMessage(), 'recharge_request');
            //失败，回滚
            $this->transRollBack();

            return false;
        }
    }

    //充值状态修改
    public function modifyRechargeStatus($orderId, $status, $errorMsg = '')
    {
        $this->errorMessage = $errorMsg;

        //开启事务
        $this->transStart();

        try {
            $this->updateStatusAndSettlement($orderId, $status);

        } catch (\Exception $e) {
            logs($e->getMessage(), 'notifyWithdraw');
            //失败，回滚
            $this->transRollBack();

            return false;
        }

        //成功
        $this->transCommit();

        return true;

    }

    /**
     * 更新订单状态，记录流水并通知结算
     *
     * @param $orderId
     * @param $status
     * @return bool
     * @throws \Exception
     */
    protected function updateStatusAndSettlement($orderId, $status)
    {
        $this->rechargeResult = $status == 1 ? self::STATUS_SUCCESS : self::STATUS_FAIL;

        $this->orderInfo = $this->getRecordingByOrderId($orderId);

        if (empty($this->orderInfo)) {
            throw new \Exception('充值订单不存在');
        }

        $this->modifyRecordingStatus($this->orderInfo['id']);

        /**
         * 成功，需要增加账户金额和通知结算，最后记录流水
         * 失败，只记录流水
         */
        if ($this->rechargeResult == self::STATUS_SUCCESS) {
            return $this->updateAvailableAndNoticeSettlement(
                $this->orderInfo['user_id'], $this->orderInfo['amount']
            );
        }

        return $this->recordStream($this->orderInfo['user_id']);
    }

    /**
     * 修改充值订单状态
     * @param $id
     * @return bool
     * @throws \Exception
     */
    protected function modifyRecordingStatus($id)
    {
        $rechargeRes = $this
            ->where("`id` = '{$id}' and `status` = " . self::STATUS_ING)
            ->upd(array(
                'status'        => $this->rechargeResult,
                'error_message' => $this->errorMessage,
                'update_time'   => date("Y-m-d H:i:s")
            ));

        if (!$rechargeRes) {
            throw new \Exception('充值订单不存在');
        }

        return true;
    }

    /**
     * 修改账户余额并通知结算
     * @param $userId
     * @param $money
     * @return bool
     */
    protected function updateAvailableAndNoticeSettlement($userId, $money)
    {
        if ($this->updateAvailableAndRecordStream($userId, $money)) {
            $this->noticeSettlement($userId);
        }

        return true;
    }

    /**
     * 修改账户余额并记录流水
     * @param $userId
     * @param $money
     * @return bool
     * @throws \Exception
     */
    protected function updateAvailableAndRecordStream($userId, $money)
    {
        $marginModel = new MarginMargin();

        //修改总资产和
        $margin['avaliable_amount'] = array(
            'action' => 'add',
            'amount' => $money
        );

        $marginData = $marginModel->updMarginReturnChange($userId, $margin);
        if (!$marginData) {
            throw new \Exception('资产数据修改失败');
        }

        return $this->recordStream($userId, $marginData);
    }

    /**
     * 对应不同充值结果，记录流水
     * @param $userId
     * @param null $marginData
     * @return bool
     * @throws \Exception
     */
    protected function recordStream($userId, $marginData = null)
    {
        if ($this->rechargeResult == self::STATUS_FAIL) {

            $marginModel = new MarginMargin();
            $marginInfo = $marginModel->getMarginByUserId($userId);

            $remark = "于 " . date("Y-m-d H:i:s") . " 充值{$this->orderInfo['amount']}元，充值失败。";
            $marginData = array(
                'before_avaliable_amount' => $marginInfo['avaliable_amount'],
                'after_avaliable_amount'  => $marginInfo['avaliable_amount']
            );
            $typename = 'recharge_fail';
            $isAffectedAvailable = MarginRecord::NOT_AFFECTED_AVALIABLE;

        } else {
            $remark = "于 " . date("Y-m-d H:i:s") . " 成功充值{$this->orderInfo['amount']}元.";
            $typename = 'recharge_success';
            $isAffectedAvailable = MarginRecord::AFFECTED_AVALIABLE;
        }

        $recordRes = MarginRecord::record(
            $this->orderInfo['user_id'],
            $this->orderInfo['uuid'],
            $this->orderInfo['id'],
            $typename,
            $this->orderInfo['amount'],
            $marginData,
            $remark,
            $isAffectedAvailable
        );

        if (!$recordRes) {
            throw new \Exception('资产流水记录失败');
        }

        return true;
    }

    /**
     * 通知结算
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    protected function noticeSettlement($userId)
    {
        $authUser = new AuthUser($userId);

        //通知结算端买入成功
        $params = array(
            'user_id'     => $authUser->id,
            'user_name'   => $authUser->username,
            'user_mobile' => $authUser->phone,
            'token'       => $this->orderInfo['uuid'],
            'amount'      => $this->orderInfo['amount'],
            'status'      => $this->rechargeResult
        );

        $response = Common::jsonRpcApiCall((object) $params, 'cashBuy', config('RPC_API.projects'));
        if (!isset($response['result']['code']) || $response['result']['code'] != 0) {
            throw new \Exception('调用结算端，现金买入活期接口调用失败');
        }

        //广播消息
        Common::messageBroadcast(['addExperience', 'recharge'], array(
            'user_id'   => $authUser->id,
            'name'      => $authUser->realname ?: $authUser->phone,
            'phone'     => $authUser->phone,
            'node_name' => 'recharge',
            'datetime'  => date("m月d日H时i分"),
            'amount'    => $this->orderInfo['amount']
        ));

        //首充送邀请人体验金
        if ($this->isFirstRecharge($userId)) {
            $this->assertIsMeetsInviteAwardConditionAndGrant($userId);
        }

        return true;
    }

    public function assertIsMeetsInviteAwardConditionAndGrant($userId)
    {
        $authUser = new AuthUser();
        $userAward = new UserAwardCounts();
        $fromUserId = $authUser->getInviteSourceUserId($userId);
        if ($fromUserId == 0) {
            return false;
        }

        $inviteGrantCounts = $userAward->getCountsByType($fromUserId, 'invite');

        if (
            $this->meetsInvitesUsersCondition($inviteGrantCounts) &&
            $this->meetsInviteRechargeAmountCondition()
        ) {
            Common::messageBroadcast('addExperience', array(
                'user_id'   => $fromUserId,
                'node_name' => 'invite',
                'time'      => date("Y-m-d H:i:s")
            ));
            $userAward->increaseGrantCounts($fromUserId, 'invite');
        }

        return true;
    }

    protected function meetsInvitesUsersCondition($inviteGrantCounts)
    {
        if (config('MAX_INVITE_AWARD', 20) == 'infinite') {
            return true;
        }

        return bccomp($inviteGrantCounts, config('AWARD_INVITE.max_counts', 20)) == -1;
    }

    protected function meetsInviteRechargeAmountCondition()
    {
        return bccomp($this->orderInfo['amount'], config('AWARD_INVITE.minimal_recharge', 100)) >= 0;
    }

    /**
     * 是否首次充值
     *
     * @param $userId
     *
     * @return bool
     */
    public function isFirstRecharge($userId)
    {
        $result = $this->fields("COUNT(*) AS `total`", false)
            ->where(array('user_id' => $userId, 'status' => self::STATUS_SUCCESS))
            ->get()->row();

        return bccomp($result->total, 1) < 1;
    }
}