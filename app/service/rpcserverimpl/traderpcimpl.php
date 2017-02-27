<?php
namespace App\service\rpcserverimpl;

use Lib;
use Model;
use  App\service\exception\AllErrorException as AllErrorException;

class TradeRpcImpl extends BaseRpcImpl
{
    /**
     * 还款
     * @JsonRpcMethod
     * @param $refund
     */
    public function refund($refund)
    {
        $refund = (array)$refund;
        $userId = I('data.userId', null, null, $refund);
        $refundId = I('data.refundId', null, null, $refund);
        $token = I('data.token', null, null, $refund);
        $amount = I('data.amount', null, null, $refund);
        $interest = I('data.interest', null, null, $refund);
        $increase = I('data.increase', null, null, $refund);
        $expInterest = I('data.expInterest', null, null, $refund);

        if (is_null($userId)  OR is_null($refundId) OR is_null($token)  OR is_null($amount) OR is_null($interest) OR is_null($increase) OR is_null($expInterest)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);//参数缺失
        }

        if ((1 === bccomp(0, $amount, 10)) OR (1 === bccomp(0, $amount, 10)) OR (1 === bccomp(0, $interest, 10)) OR (1 === bccomp(0, $increase, 10)) OR (1 === bccomp(0, $expInterest, 10))) {
            throw new AllErrorException(AllErrorException::API_ERROR_PARAMS);//参数错误
        }

        $UserRefundModel = new Model\MarginRefund();
        $refundFields = [
            'user_id' => $userId,
            'refund_id' => $refundId,
            'uuid' => $token,
            'amount' => $amount,
            'interest' => $interest,
            'increase' => $increase,
            'exp_interest' => $expInterest,
        ];
        return $UserRefundModel->refund($refundFields);

    }

    /**
     * 获取交易流水
     * @JsonRpcMethod
     * @param $params
     * @throws AllErrorException
     */
    public function tradeRecord($params)
    {
        $params = (array)$params;

        if (($this->userId = $this->checkLoginStatus()) === false)
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);//检查登录状态
        $userId = $this->userId;


        $type = I('data.type/d', 0, null, $params);
        $page = I('data.page/d', 1, null, $params);

        $marginRecordModel = new \Model\MarginRecord();
        $pagination = new \Lib\Pagination([
            'total' => $marginRecordModel->getByTypeCountNums($userId, $type),
            'pagesize' => C('PAGE_SIZE'),
            'current_page' => $page
        ]);

        $data = new \stdClass();

        if (($page > $pagination->page_total) OR ($page < 1)) {
            $data->list = new \stdClass();
            $data->page = $page;
            $data->pageTotal = $pagination->page_total;

        } else {
            $marginRecords = $marginRecordModel->getByType($userId, $type, $pagination->start, $pagination->offset);
            $data->list = $marginRecordModel->sortByMonth($marginRecords);
            $data->page = $pagination->current_page;
            $data->pageTotal = $pagination->page_total;
        }
        return ['code' => 0, 'data' => $data];

    }


    /**
     * 获取交易流水
     * @JsonRpcMethod
     * @param $params
     * @throws AllErrorException
     */
    public function tradeRecordPC($params)
    {
        $params = (array)$params;
        $type = I('data.type/d', 0, null, $params);
        $days = I('data.days/d', 0, null, $params);
        $startTime = I('data.start_time/s', '', null, $params);
        $endTime = I('data.end_time/s', '', null, $params);
        $page = I('data.page/d', 1, null, $params);

        if (($this->userId = $this->checkLoginStatus()) === false)
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);//检查登录状态
        $userId = $this->userId;


        if ($days) {
            $startTime = date("Y-m-d 00:00:00", strtotime('-' . $days . ' day'));
            $endTime = date("Y-m-d 23:59:59");
        } elseif ($startTime && $endTime) {
            $startTime = date("Y-m-d 00:00:00", strtotime($startTime));
            $endTime = date("Y-m-d 23:59:59", strtotime($endTime));
        } else {
            $startTime = '';
            $endTime = '';
        }

        $marginRecordModel = new \Model\MarginRecord();
        $pagination = new \Lib\Pagination([
            'total' => $marginRecordModel->getByTypeAndTimeCountNums($userId, $type, $startTime, $endTime),
            'pagesize' => C('PAGE_SIZE'),
            'current_page' => $page
        ]);

        $data = new \stdClass();

        if (($page > $pagination->page_total) OR ($page < 1)) {
            $data->list = new \stdClass();
            $data->page = $page;
            $data->pageTotal = $pagination->page_total;

        } else {
            $data->list = $marginRecordModel->getByTypeAndTime($userId, $type, $startTime, $endTime, $pagination->start, $pagination->offset);
            $data->page = $pagination->current_page;
            $data->pageTotal = $pagination->page_total;
        }
        return ['code' => 0, 'data' => $data];

    }

    /**
     * 获取交易流水详情
     * @JsonRpcMethod
     * @param $recordId
     * @throws AllErrorException
     */
    public function tradeRecordDetail($params)
    {
        $params = (array)$params;

        if (($this->userId = $this->checkLoginStatus()) === false)
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);//检查登录状态
        $userId = $this->userId;


        $recordId = $type = I('data.record_id', null, null, $params);
        if (is_null($recordId)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);//参数缺失
        }

        $marginRecordModel = new \Model\MarginRecharge();
        $record = $marginRecordModel->getFailByUserAndId($userId, $recordId);
        if ($record) {
            $record->type_to_cn = '充值';
            $record->fee = '0.00';
        } else {
            $record = [];
        }
        return ['code' => 0, 'data' => $record];

    }
}