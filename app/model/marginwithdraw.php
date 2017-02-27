<?php
namespace Model;

use App\service\rpcserverimpl\Common;

class MarginWithdraw extends Model
{

    /**
     * 提现状态 100：待审核  110：审核完成  200：转出成功 400：转出失败
     */
    const STATUS_ING = 100;

    const STATUS_AUDITED = 110;

    const STATUS_AUDITED_FAIL = 120;

    const STATUS_SUCCESS = 200;

    const STATUS_FAIL = 400;

    /**
     * 审核方式  1：人工  0：自动
     */
    const MODE_MANUAL = 1;
    const MODE_AUTO = 0;

    protected $orderInfo;
    //给用户展示的提现状态
    protected $withdrawResult;

    //提现订单真正的状态
    protected $realResult;

    protected $errorMessage;

    public function __construct($pkVal = '')
    {
        parent::__construct('margin_withdraw');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function getInfo($id)
    {
        return $this->where("id = {$id}")->get()->rowArr();
    }

    //获取提现数据
    public function getRecordingByOrderId($orderId)
    {
        return $this->where("`order_id` = '{$orderId}'")->get()->rowArr();
    }

    //获取当月免手续费次数
    public function getFreeWithdrawNum($userId)
    {
        $couterModel = new MarginRefundCounter();

        //每月免基本手续费次数
        $totalNum = FREE_WITHDRAW_NUM;

        //当月的已免手续费计数
        $count = $couterModel->getCounter($userId, date("Y-m-d H:i:s"));

        if ($count >= $totalNum) {
            return 0;
        }

        return $totalNum - $count;
    }

    //获取某月需要退手续费的提现记录
    public function getRefundWithdraw($userId, $datetime)
    {
        $start_time = date('Y-m-01 00:00:00', strtotime($datetime));
        $end_time = date('Y-m-32 00:00:00', strtotime($datetime));

        $sql = "select * from " . $this->tableName . " where `create_time`>'{$start_time}' and `create_time`<'{$end_time}' and  `status` = '200' and user_id = '{$userId}' and `fee` > 0 and `refund_status` = 0";

        return $this->query($sql)->rowArr();
    }

    //获取当日已提现总额
    public function getDailyWithdrawAmount($userId, $datetime)
    {
        $start_time = date('Y-m-d 00:00:00', strtotime($datetime));
        $end_time = date('Y-m-d 23:59:59', strtotime($datetime));

        $row = $this
            ->fields('SUM(source_amount) as amount', false)
            ->where("user_id = '{$userId}' and `create_time`>'{$start_time}' and `create_time`<='{$end_time}' and `status` != 400")
            ->get()->rowArr();

        if (empty($row)) {
            return '0.00';
        }

        return $row['amount'];
    }

    /**
     * 添加提现记录
     *
     * @param $userId
     * @param $uuid
     * @param $amount
     *
     * @return array|bool
     */
    public function addRecording($userId, $uuid, $amount)
    {
        //模型实例化
        $bankCardModel = new AuthBankCard();
        $authUserModel = new AuthUser($userId);
        $marginModel = new MarginMargin();
        $couterModel = new MarginRefundCounter();

        //获取手续费
        $feeData = $marginModel->getWithdrawFee($userId, $amount);

        //身份信息和银行卡数据
        $bankCardInfo = $bankCardModel->getBindCard($userId);

        //提现记录
        $withdrawData['uuid'] = $uuid;                                      //唯一id
        $withdrawData['order_id'] = generate_orderid(PREFIX_WITHDRAE_ID);   //订单号
        $withdrawData['user_id'] = $userId;                                 //用户id
        $withdrawData['realname'] = $authUserModel->realname;               //用户真实姓名
        $withdrawData['id_number'] = $authUserModel->id_number;             //身份证号
        $withdrawData['phone'] = $authUserModel->phone;                     //电话号码
        $withdrawData['source_amount'] = $amount;                           //提现金额
        $withdrawData['amount'] = $feeData['actual_amount'];                //实际到账
        $withdrawData['fee'] = $feeData['total_fee'];                       //基本手续费
        $withdrawData['bank_account'] = $bankCardInfo['cardno'];            //银行账号
        $withdrawData['bank_code'] = $bankCardInfo['bankcode'];             //银行代码
        $withdrawData['bank_name'] = $bankCardInfo['bankname'];             //银行名称
        $withdrawData['from_platform'] = getUAInfo('platform');             //发起平台
        $withdrawData['client_ip'] = get_client_ip();
        $withdrawData['create_time'] = date("Y-m-d H:i:s");
        $withdrawData['update_time'] = date("Y-m-d H:i:s");

        //开启事务
        $this->transStart();

        try {
            //1、增加提现记录
            $withdrawRes = $this->add($withdrawData);
            if (!$withdrawRes) {
                throw new \Exception('添加提现记录失败');
            }

            //2、修改总资产和
            $marginData = array(
                'avaliable_amount'   => array(
                    'action' => 'sub',
                    'amount' => $amount
                ),
                'withdrawing_amount' => array(
                    'action' => 'add',
                    'amount' => $amount
                )
            );
            $marginRes = $marginModel->updMarginReturnChange($userId, $marginData);
            if (!$marginRes) {
                throw new \Exception('资产数据修改失败');
            }

            //3、提现流水记录
            $remark = "于 " . date("Y-m-d H:i:s") . " 申请转出{$amount}元.";
            $recordRes = MarginRecord::record($userId, $withdrawData['uuid'], $withdrawRes, 'withdraw_request', -$amount, $marginRes, $remark);
            if (!$recordRes) {
                throw new \Exception('资产流水记录失败');
            }
            //4、通知结算端审核中（100）
            $params = array(
                'user_id'     => $userId,
                'user_name'   => $authUserModel->username,
                'user_mobile' => $authUserModel->phone,
                'token'       => $withdrawData['uuid'],
                'amount'      => $withdrawData['source_amount'],
                'status'      => self::STATUS_ING
            );
            $response = Common::jsonRpcApiCall((object) $params, 'cashSell', config('RPC_API.projects'), false);
            if (!isset($response['result']['code']) || $response['result']['code'] != 0) {
                throw new \Exception('调用结算端，现金转出活期接口调用失败');
            }

            //提交事务
            $transStatus = $this->transCommit();
            if (!$transStatus) {
                throw new \Exception('事务提交失败');
            }

            //免手续费加次数
            if (bccomp($feeData['basic_fee'], 0) == 0) {
                $couterModel->incrementCounter($userId, date("Y-m-d H:i:s"));
            }

            //用户数据缓存失效
            invalidUserProfileCache($userId);

            return $withdrawRes;

        } catch (\Exception $e) {

            $errorMessage = '添加提现记录，错误信息：' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'sql_1：' . PHP_EOL . $this->getLastQuery() . PHP_EOL;
            $errorMessage .= 'sql_2：' . PHP_EOL . $marginModel->getLastQuery() . PHP_EOL;
            $errorMessage .= 'api_params：' . PHP_EOL . var_export($params, true) . PHP_EOL;
            $errorMessage .= 'api_reponse：' . PHP_EOL . var_export($response, true) . PHP_EOL;
            logs($errorMessage, 'withdraw_request');
            //失败，回滚
            $this->transRollBack();

            return false;
        }
    }

    //审核完成
    public function auditComplete($id, $isManual)
    {
        $marginModel = new MarginMargin();

        $recording = $this->getInfo($id);
        if (empty($recording)) {
            logs('获取转出记录出错：' . PHP_EOL . $this->getLastQuery() . PHP_EOL, 'withdraw');
            throw new \Exception('转出记录不存在！');
        }

        $marginInfo = $marginModel->getMarginByUserId($recording['user_id']);

        //开启事务
        $this->transStart();

        try {
            $auditMode = $isManual ? self::MODE_MANUAL : self::MODE_AUTO;

            //1、更新审核模式
            $withdrawRes = $this
                ->where(array('id' => $id, 'is_manual' => self::MODE_MANUAL))
                ->upd(array(
                    'status'      => self::STATUS_AUDITED,
                    'real_status' => self::STATUS_AUDITED,
                    'is_manual'   => $auditMode,
                    'update_time' => date("Y-m-d H:i:s")
                ));

            if ($withdrawRes === false) {
                throw new \Exception('修改审核模式失败');
            }

            //2、记录审核完成流水
            $marginRecData = array(
                'before_avaliable_amount' => $marginInfo['avaliable_amount'],
                'after_avaliable_amount'  => $marginInfo['avaliable_amount']
            );
            $remark = "资金转出于 " . date("Y-m-d H:i:s") . " 审核完成。";
            $recordRes = MarginRecord::record(
                $recording['user_id'],
                $recording['uuid'],
                $recording['id'],
                'withdraw_ing',
                -$recording['source_amount'],
                $marginRecData,
                $remark,
                MarginRecord::NOT_AFFECTED_AVALIABLE,
                self::STATUS_SUCCESS
            );
            if (!$recordRes) {
                throw new \Exception('记录审核流水失败');
            }

            //提交事务
            $transStatus = $this->transCommit();
            if (!$transStatus) {
                throw new \Exception('事务提交失败');
            }

            return true;

        } catch (\Exception $e) {

            $errorMessage = '修改审核状态，错误信息：' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'sql_1：' . PHP_EOL . $this->getLastQuery() . PHP_EOL;
            logs($errorMessage, 'withdraw');
            //失败，回滚
            $this->transRollBack();

            return false;
        }
    }

    public function auditRefuse($id, $errorMessage = '无')
    {
        $marginModel = new MarginMargin();

        $this->withdrawResult = self::STATUS_FAIL;

        $this->orderInfo = $this->getInfo($id);

        if (empty($this->orderInfo)) {
            logs('获取转出记录出错：' . PHP_EOL . $this->getLastQuery() . PHP_EOL, 'withdraw');
            throw new \Exception('转出记录不存在！');
        }

        //开启事务
        $this->transStart();

        try {
            //1、更新审核模式
            $withdrawRes = $this
                ->where("`id` = '{$id}' and `status` = " . self::STATUS_ING)
                ->upd(array(
                    'status'      => self::STATUS_FAIL,
                    'real_status' => self::STATUS_FAIL,
                    'update_time' => date("Y-m-d H:i:s")
                ));

            if (!$withdrawRes) {
                throw new \Exception('修改审核模式失败');
            }

            //2、修改总资产
            $margin = array(
                'avaliable_amount'   => array(
                    'action' => 'add',
                    'amount' => $this->orderInfo['source_amount']
                ),
                'withdrawing_amount' => array(
                    'action' => 'sub',
                    'amount' => $this->orderInfo['source_amount']
                )
            );
            $marginRecData = $marginModel->updMarginReturnChange($this->orderInfo['user_id'], $margin);

            $remark = "审核失败，失败原因：" . $errorMessage;
            $recordRes = MarginRecord::record(
                $this->orderInfo['user_id'],
                $this->orderInfo['uuid'],
                $this->orderInfo['id'],
                'audit_fail',
                $this->orderInfo['source_amount'],
                $marginRecData,
                $remark,
                MarginRecord::AFFECTED_AVALIABLE,
                self::STATUS_FAIL
            );
            if (!$recordRes) {
                throw new \Exception('记录审核流水失败');
            }

            //通知结算端
            $this->noticeSettlement($this->orderInfo['user_id']);

            //提交事务
            $transStatus = $this->transCommit();
            if (!$transStatus) {
                throw new \Exception('事务提交失败');
            }

            return true;

        } catch (\Exception $e) {

            $errorMessage = '修改审核状态，错误信息：' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'sql_1：' . PHP_EOL . $this->getLastQuery() . PHP_EOL;
            logs($errorMessage, 'withdraw');
            //失败，回滚
            $this->transRollBack();

            return false;
        }
    }

    /**
     * 修改提现订单状态
     * @param $orderId
     * @param $status
     * @param string $errorMsg
     * @return bool
     */
    public function modifyWithdrawStatus($orderId, $status, $errorMsg = '')
    {
        if ($status != 0 && $status != 1) {
            return true;
        }
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

        //发送消息通知
        if ($status != 3) {
            Common::messageBroadcast('withdraw', array(
                'user_id'     => $this->orderInfo['user_id'],
                'withdraw_id' => $this->orderInfo['id'],
                'type'        => $this->withdrawResult == 200 ? 'withdraw_success' : 'withdraw_fail',
                'datetime'    => date("m月d日")
            ));
        }

        return true;
    }

    //退还当月手续费
    public function refundFee($userId, $datetime)
    {
        //免手续费计数
        $couterModel = new MarginRefundCounter();
        $marginModel = new MarginMargin();
        $count = $couterModel->getCounter($userId, $datetime);

        if ($count < FREE_WITHDRAW_NUM) {
            //获取要退手续费的那笔提现
            $refundWithdraw = $this->getRefundWithdraw($userId, $datetime);
            if (!empty($refundWithdraw)) {
                //开启事务
                $this->transStart();

                //1、退还手续费（账户余额）
                $marginData = array(
                    'avaliable_amount' => array(
                        'action' => 'add',
                        'amount' => $refundWithdraw['fee']
                    )
                );
                $marginRes = $marginModel->updMarginReturnChange($userId, $marginData);

                //2、提现失败退还手续费
                $remark = "提现失败退还手续费{$refundWithdraw['fee']}元。";
                $recordRes = MarginRecord::record($userId, create_guid(), $refundWithdraw['id'], 'refund_fee', $refundWithdraw['fee'], $marginRes, $remark);

                //3、标记退还手续费提现订单
                $refundS = $this->update(array('refund_status' => 1), array('id' => $refundWithdraw['id']));

                //全部成功
                if ($marginRes && $recordRes && $refundS) {
                    $this->transCommit();
                    //更新次数
                    $couterModel->incrementCounter($userId, $datetime);

                    //用户数据缓存失效
                    invalidUserProfileCache($userId);

                    return true;
                } else {
                    //失败，回滚
                    $this->transRollBack();

                    return false;
                }
            }
        }
    }

    //转出交易记录
    public function getUserRecords($userId)
    {
        return $this->fields("id, uuid, '0' as 'type', source_amount as amount, bank_account, bank_name, update_time as datetime, status", false)
            ->where("`user_id` = {$userId}")
            ->get()->resultArr();
    }

    /**
     * 更新订单状态并通知结算端
     *
     * @param $orderId
     * @param $status
     * @return bool
     * @throws \Exception
     */
    protected function updateStatusAndSettlement($orderId, $status)
    {
        $this->withdrawResult = $status == 1 ? self::STATUS_SUCCESS : self::STATUS_FAIL;
        $this->realResult = $status == 1 ? self::STATUS_SUCCESS : self::STATUS_FAIL;

        $this->orderInfo = $this->getRecordingByOrderId($orderId);

        if (empty($this->orderInfo)) {
            throw new \Exception('充值订单不存在');
        }

        //提现订单已经处理过
        if ($this->recordHasProcessed($this->orderInfo)) {
            return $this->modifyRecordingRealStatus($this->orderInfo['id']);
        }

        //修改订单状态，status和real_status
        $this->modifyRecordingStatus($this->orderInfo['id']);

        //更新可用余额
        $marginData = $this->updateAvailable(
            $this->orderInfo['user_id'], $this->orderInfo['source_amount'], $this->withdrawResult
        );

        //记录流水并通知结算端
        return $this->recordStreamAndNoticeSettlement($this->orderInfo['user_id'], $marginData);
    }

    /**
     * 判断是否处理过订单(第一次处理【有可能假结果】固定是成功)
     *
     * @param $orderInfo
     * @return bool
     */
    protected function recordHasProcessed($orderInfo)
    {
        return $orderInfo['status'] == self::STATUS_SUCCESS;
    }

    /**
     * 修改真实的提现订单状态
     *
     * @param $id
     * @return bool
     */
    protected function modifyRecordingRealStatus($id)
    {
        $withdrawRes = $this
            ->where("`id` = '{$id}'")
            ->upd(array(
                'real_status' => $this->realResult,
                'update_time' => date("Y-m-d H:i:s")
            ));

        return true;
    }

    /**
     * 修改订单状态
     *
     * @param $id
     * @return bool
     * @throws \Exception
     */
    protected function modifyRecordingStatus($id)
    {
        $withdrawRes = $this
            ->where("`id` = '{$id}' and `status` = " . self::STATUS_AUDITED)
            ->upd(array(
                'status'        => $this->withdrawResult,
                'real_status'   => $this->realResult,
                'error_message' => $this->errorMessage,
                'update_time'   => date("Y-m-d H:i:s")
            ));

        if (!$withdrawRes) {
            throw new \Exception('提现订单不存在');
        }

        return true;
    }

    /**
     * 更新可用余额
     * @param $userId
     * @param $money
     * @return bool|mixed
     * @throws \Exception
     */
    protected function updateAvailable($userId, $money)
    {
        $marginModel = new MarginMargin();
        if ($this->withdrawResult == self::STATUS_FAIL) {
            $margin['avaliable_amount'] = array(
                'action' => 'add',
                'amount' => $money
            );
        }
        $margin['withdrawing_amount'] = array(
            'action' => 'sub',
            'amount' => $money
        );

        $marginData = $marginModel->updMarginReturnChange($userId, $margin);
        if (!$marginData) {
            throw new \Exception('资产数据修改失败');
        }

        return $marginData;
    }

    /**
     * 记录流水并通知结算端
     * @param $userId
     * @param $marginData
     * @return bool
     */
    protected function recordStreamAndNoticeSettlement($userId, $marginData)
    {
        if ($this->recordStream($userId, $marginData)) {
            return $this->noticeSettlement($userId);
        }

        return false;
    }

    /**
     * 记录流水
     * @param $userId
     * @param $marginData
     * @return bool
     * @throws \Exception
     */
    protected function recordStream($userId, $marginData)
    {

        if ($this->withdrawResult == self::STATUS_FAIL) {
            $remark = "提现失败退款{$this->orderInfo['source_amount']}元。";
            $typename = 'withdraw_fail';
            $isAffectedAvailable = MarginRecord::AFFECTED_AVALIABLE;
        } else {
            $marginModel = new MarginMargin();
            $marginInfo = $marginModel->getMarginByUserId($userId);

            $marginData = array(
                'before_avaliable_amount' => $marginInfo['avaliable_amount'],
                'after_avaliable_amount'  => $marginInfo['avaliable_amount']
            );
            $remark = "于 " . date("Y-m-d H:i:s") . " 成功提现{$this->orderInfo['amount']}元.";
            $typename = 'withdraw_success';
            $isAffectedAvailable = MarginRecord::NOT_AFFECTED_AVALIABLE;
        }

        $recordRes = MarginRecord::record(
            $this->orderInfo['user_id'],
            $this->orderInfo['uuid'],
            $this->orderInfo['id'],
            $typename,
            $this->orderInfo['source_amount'],
            $marginData,
            $remark,
            $isAffectedAvailable,
            $this->withdrawResult
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
            'amount'      => $this->orderInfo['source_amount'],
            'status'      => $this->withdrawResult
        );

        $response = Common::jsonRpcApiCall((object) $params, 'cashSell', config('RPC_API.projects'));
        if (!isset($response['result']['code']) || $response['result']['code'] != 0) {
            throw new \Exception('调用结算端，现金转出接口调用失败');
        }

        return true;
    }
}