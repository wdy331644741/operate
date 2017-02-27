<?php
namespace Model;

use  App\service\exception\AllErrorException as AllErrorException;

class MarginRefund extends Model
{
    const STATUS_SUCCESS = 200;
    const STATUS_ING = 100;
    const STATUS_WAITRETRY = 101;

    const PRODUCT_TYPE_SANBIAO = 1;
    const PRODUCT_TYPE_YUELIBAO = 2;

    public function __construct($pkVal = '')
    {
        parent::__construct('margin_refund');
        if ($pkVal)
            $this->initArData($pkVal);
    }


    public function getByRefundId($refundId)
    {
        $row = $this->fields(['refund_id'])->where(['refund_id' => $refundId])->get()->rowArr();
        if (isset($row['refund_id']) && ($row['refund_id'] == $refundId))
            return $refundId;
        else
            return false;
    }

    public function refund($plan)
    {
        $localRefundId = $this->getByRefundId($plan['refund_id']);
        if ($localRefundId) {
            return $localRefundId;
        } else {
            try {
                $this->transStart();
                $datetime = date('Y-m-d H:i:s');
                $marginMarginModel = new MarginMargin();

                $remark = "于 {$datetime} 还款 {$plan['amount']} 元,其中利息 {$plan['interest']} 元,加息 {$plan['increase']} 元,体验金利息 {$plan['exp_interest']} 元。";

                $plan['remark'] = $remark;
                $plan['status'] = self::STATUS_SUCCESS;
                $plan['create_time'] = $datetime;
                $this->add($plan);
                $localRefundId = $this->getByRefundId($plan['refund_id']);
                if (false == $localRefundId)
                    throw new AllErrorException(AllErrorException::CREATE_REFUNDORDER_FAIL); //创建还款订单失败

                //利息
                if (-1 === bccomp(0, $plan['interest'], 10)) {
                    $amountData = [
                        'avaliable_amount' => ['action' => 'add', 'amount' => $plan['interest']],//增加可用余额
                    ];
                    $marginChange = $marginMarginModel->updMarginReturnChange($plan['user_id'], $amountData);
                    if (!$marginChange)
                        throw new AllErrorException(AllErrorException::UPDATE_USER_MARGIN_FAIL); //更新用户资产失败

                    $remark = "利息入账 {$plan['interest']} 元.";
                    $recordId = MarginRecord::record($plan['user_id'], $plan['uuid'], $plan['refund_id'], 'refund_interest', $plan['interest'], $marginChange, $remark);
                    if (!$recordId)
                        throw new AllErrorException(AllErrorException::REFUND_INTEREST_FAIL);//利息入账失败
                }

                //加息
                if (-1 === bccomp(0, $plan['increase'], 10)) {
                    $amountData = [
                        'avaliable_amount' => ['action' => 'add', 'amount' => $plan['increase']],//增加可用余额
                    ];
                    $marginChange = $marginMarginModel->updMarginReturnChange($plan['user_id'], $amountData);
                    if (!$marginChange)
                        throw new AllErrorException(AllErrorException::UPDATE_USER_MARGIN_FAIL); //更新用户资产失败

                    $remark = "加息入账 {$plan['increase']} 元.";
                    $recordId = MarginRecord::record($plan['user_id'], $plan['uuid'], $plan['refund_id'], 'refund_increase', $plan['increase'], $marginChange, $remark);
                    if (!$recordId)
                        throw new AllErrorException(AllErrorException::REFUND_INCREASE_FAIL);//加息入账失败
                }

                //体验金利息
                if (-1 === bccomp(0, $plan['exp_interest'], 10)) {
                    $amountData = [
                        'avaliable_amount' => ['action' => 'add', 'amount' => $plan['exp_interest']],//减少可用余额
                    ];
                    $marginChange = $marginMarginModel->updMarginReturnChange($plan['user_id'], $amountData);
                    if (!$marginChange)
                        throw new AllErrorException(AllErrorException::UPDATE_USER_MARGIN_FAIL); //更新用户资产失败

                    $remark = "体验金利息入账 {$plan['exp_interest']} 元.";
                    $recordId = MarginRecord::record($plan['user_id'], $plan['uuid'], $plan['refund_id'], 'refund_exp_interest', $plan['exp_interest'], $marginChange, $remark);
                    if (!$recordId)
                        throw new AllErrorException(AllErrorException::REFUND_EXPINTEREST_FAIL);//体验金利息入账失败
                }



                //提交事务
                $transStatus = $this->transCommit();
                if (!$transStatus)
                    throw new AllErrorException(AllErrorException::REFUND_FAIL);//还款失败

                return $plan['refund_id'];

            } catch (\Exception $e) {
                $this->transRollBack();//回滚事务
                $this->setError($e->getCode(), $e->getMessage());
                logs($this->getError(), 'refund');
                return false;
            }
        }

    }

}