<?php
namespace Model;
use App\service\exception\AllErrorException;
class MarginMargin extends Model
{
    const   ACTION_SUB_SYMBOL = '-';
    const   ACTION_ADD_SYMBOL = '+';

    public function __construct($pkVal = '')
    {
        parent::__construct('margin_margin');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    /**
     * 初始化用户资产
     * @param $userId
     */
    public function initMarginData($userId)
    {
        $marginInfo = $this->getMarginByUserId($userId);

        if (empty($marginInfo)) {
            $this->add(array('user_id' => $userId));
        }
    }

    /**
     * 获取用户资产
     * @param $userId
     * @return mixed
     */
    public function getMarginByUserId($userId)
    {
        $userMargin = $this->where(['user_id' => $userId])->get()->rowArr();
        return $userMargin;
    }

    /**
     * 获取提现手续费各个组成
     * @param $userId
     * @param $amount
     * @param $is_company
     *
     * @return array
     */
    public function getWithdrawFee($userId, $amount)
    {
        $feeData = array();

        //基本手续费
        $feeData['basic_fee'] = $this->getBasicFee($userId, $amount);
        $feeData['total_fee'] = $feeData['basic_fee'];
        $feeData['actual_amount'] = bcsub($amount, $feeData['total_fee'], 2);
        return $feeData;
    }

    /**
     * 获取基本手续费
     * @param $userId
     * @param $amount
     * @return int
     */
    public function getBasicFee($userId, $amount)
    {
        $withdrawModel = new MarginWithdraw();

        //获取免手续费次数
        $freeDrawNum = $withdrawModel->getFreeWithdrawNum($userId);

        if ($freeDrawNum <= 0) {
            return '2.00';
        }
        return '0.00';
    }

    /**
     * 更新用户资产，条件限定说明：1.所更新的资产必须等于更新之前查出来的，2.扣除之后不准小于0.00
     * @param $userId
     * @param $amountData
     * @return bool|mixed
     */
    public function updMarginReturnChange($userId, $amountData)
    {
        $userMargin = $this->getMarginByUserId($userId);
        if ($userMargin) {
            $marginDefault = [];
            foreach ($userMargin as $marginIndex => $marginItem) {
                $marginDefault['after_' . $marginIndex] = $marginDefault['before_' . $marginIndex] = $marginItem;
            }

            $pkWhere = ['user_id' => $userId];
            $whereData = $updateMarginData = [];
            foreach ($amountData as $marginField => $amountDetail) {
                if ($amountDetail['action'] == 'add') {
                    $updateMarginData[$marginField] = $marginField . self::ACTION_ADD_SYMBOL . $amountDetail['amount'];
                    $marginDefault['after_' . $marginField] = bcadd($userMargin[$marginField], $amountDetail['amount'], 10);
                } else {
                    $updateMarginData[$marginField] = $marginField . self::ACTION_SUB_SYMBOL . $amountDetail['amount'];
                    $marginDefault['after_' . $marginField] = bcsub($userMargin[$marginField], $amountDetail['amount'], 10);
                    //不准为负的资产，如果本次减运算后小0.00，则报错。
                    if (in_array($marginField, ['avaliable_amount', 'principal_amount', 'withdrawing_amount', 'invset_amount']) && (1 === bccomp('0.00', $marginDefault['after_' . $marginField], 10))) {
                        $this->setError(AllErrorException::UPDATE_USER_MARGIN_FAIL, "用户Id：{$userId}，资产项：{$marginField} {$userMargin[$marginField]}元，不足本次扣除{$amountDetail['amount']}元。");
                        logs($this->getError(), 'MarginMargin');
                        return false;
                    }
                }
                $whereData[$marginField] = $marginDefault['before_' . $marginField];
            }

            $updateStatus = $this->where(array_merge($pkWhere, $whereData))->upd($updateMarginData);
            if (false != $updateStatus)
                return $marginDefault;
        }
        return false;

    }
}