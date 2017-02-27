<?php


namespace App\service\rpcserverimpl;

use App\service\exception\AllErrorException;

class OrderStatus {

    //接口充值失败错误码
    const API_RECHARGE_FAILED_CODE = 20100;

    const BINDCARD_FAILD_CODE = 20030;

    const NOT_PRE_BINDCARD = 20040;

    //失败状态
    const API_RECHARGE_FAILED_STATUS = 0;
    //成功状态
    const API_RECHARGE_SUCCESS_STATUS = 1;
    //处理中状态
    const API_RECHARGE_DEALING_STATUS = 3;

    private static $instance;

    //订单号
    protected $orderId;

    protected $requestId;

    protected $data;

    //接口提示信息
    protected $message;

    public function __construct($userId, $orderId, $optionalChannel, $requestId)
    {
        $this->userId = $userId;
        $this->orderId = $orderId;
        $this->requestId = $requestId;
        $this->data = [
            'showChannelBtn' => $optionalChannel
        ];
    }

    public static function getInstance($userId, $orderId, $optionalChannel, $requestId)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($userId, $orderId, $optionalChannel, $requestId);
        }

        return self::$instance;
    }

    //解析银行接口充值结果
    public static function getRechargeStatus(
        $apiResult, $userId, $orderId, $optionalChannel = false, $requestId = ''
    )
    {
        //获取实例
        $instance = self::getInstance($userId, $orderId, $optionalChannel, $requestId);

        //服务器错误
        if (isset($apiResult['error']) && $apiResult['error']['code'] < 0) {
            throw new AllErrorException(AllErrorException::SERVER_ERROR);
        }

        //error process
        if (isset($apiResult['error'])) {
            $errorMsg = isset($apiResult['error']['data']['res_msg']) ? $apiResult['error']['data']['res_msg'] : $apiResult['error']['message'];
            //接口提示信息
            $instance->apiErrorMsg($errorMsg);

            //处理接口返回结果
            return $instance->errorProcess($apiResult['error']);
        }

        //result process
        if (isset($apiResult['result'])) {
            //接口提示信息
            $instance->apiErrorMsg($apiResult['result']['resp_msg']);

            return $instance->resultProcess($apiResult['result']);
        }
    }

    //接口result 处理
    public function resultProcess($result)
    {
        //绑卡并充值接口 绑卡状态更新
        $this->updateBindStatus();

        if (isset($result['status'])) {

            //接口提示信息
            $message = $this->getErrorMsg();

            //订单状态修改
            $rechargeStatus = $this->updateRechargeStatus($result['status'], $message);
            //修改失败
//            if (!$rechargeStatus) {
//                throw new AllErrorException();
//            }

            //充值失败
            if ($result['status'] == self::API_RECHARGE_FAILED_STATUS) {
                //绑卡并充值，绑卡成功充值失败
                if (!empty($this->requestId)) {
                    throw new AllErrorException(AllErrorException::BINDCARD_NOT_RECHARGE);
                }
                //充值失败
                throw new AllErrorException(AllErrorException::RECHARGE_FAIL, $this->data, $message);
            }

            //充值成功
            if ($result['status'] == self::API_RECHARGE_SUCCESS_STATUS) {
                return array(
                    'code'     => 0,
                    'message'  => '充值成功',
                    'order_id' => $this->orderId
                );
            }

            //充值处理中
            if ($result['status'] == self::API_RECHARGE_DEALING_STATUS) {
                //绑卡成功，充值状态处理中
                $message = empty($this->requestId) ? "交易已提交，请稍后查询我的账户" : "信息校验完成，交易已提交，请稍后查询我的账户";

                //充值处理中
                return array(
                    'code'     => AllErrorException::RECHARGE_DEALING,
                    'message'  => $message,
                    'order_id' => $this->orderId
                );
            }
        }
    }

    //接口error处理(充值状态按失败处理)
    public function errorProcess($error)
    {
        //接口提示信息
        $message = $this->getErrorMsg();

        //订单状态修改
        $this->updateRechargeStatus(self::API_RECHARGE_FAILED_STATUS, $message);

        //绑卡状态，中断
        if (
            $error['code'] == self::BINDCARD_FAILD_CODE ||
            $error['code'] == self::NOT_PRE_BINDCARD
        ) {
            //绑卡失败
            throw new AllErrorException(
                AllErrorException::BINDCARD_FAIL, [], $message
            );
        }

        //充值失败
        if ($error['code'] == self::API_RECHARGE_FAILED_CODE) {
            //绑卡成功，充值失败
            if (!empty($this->requestId)) {
                //绑卡并充值接口 绑卡状态更新
                $this->updateBindStatus();

                throw new AllErrorException(AllErrorException::BINDCARD_NOT_RECHARGE);
            }
        }

        //充值失败
        throw new AllErrorException(AllErrorException::RECHARGE_FAIL, $this->data, $message);
    }

    public function apiErrorMsg($msg)
    {
        $this->message = $msg;
    }

    //接口提示信息
    public function getErrorMsg()
    {
        return isset($this->message) ? $this->message : '';
    }

    //仅绑卡并充值接口调用，更新绑卡状态
    protected function updateBindStatus()
    {
        if (!empty($this->requestId)) {
            $bindReqModel = new \Model\BindcardRequest();
            $bankCardModel = new \Model\AuthBankCard();

            //获取绑卡信息
            $reqLog = $bindReqModel->getReqLogByReqId($this->requestId);

            //非法，无效的绑卡请求
            if (empty($reqLog)) {
                throw new AllErrorException(AllErrorException::API_ILLEGAL);
            }

            //修改绑卡请求状态
            $res = $bankCardModel->addCardByReqLog($reqLog);
            if ($res === false) {
                //绑卡失败
                throw new AllErrorException(AllErrorException::SAVE_BIND_CARD_FAIL);
            }
        }

    }

    //更新订单充值状态
    protected function updateRechargeStatus($status, $message)
    {
        //成功或失败，修改数据库订单状态
        if (
            $status == self::API_RECHARGE_SUCCESS_STATUS ||
            $status == self::API_RECHARGE_FAILED_STATUS
        ) {
            $rechargeModel = new \Model\MarginRecharge();

            //修改订单状态
            return $rechargeModel->modifyRechargeStatus(
                $this->orderId, $status, $message
            );
        }

        return true;
    }

}