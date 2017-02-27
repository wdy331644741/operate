<?php
namespace Model;

class MarginRecord extends Model
{

    const ACTION_ADD = 1;
    const ACTION_SUB = -1;
    const STATUS_SUCCESS = 200;
    const STATUS_FAIL = 400;

    const NOT_AFFECTED_AVALIABLE = 0; //不影响可用余额
    const AFFECTED_AVALIABLE = 1; //影响可用余额

    public function __construct($pkVal = '')
    {
        parent::__construct('margin_record');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    static public function record($userId, $uuid, $recordId, $typeName, $amount, $amountData, $remark, $isAffectedAmount = 1, $status = null)
    {
        $recordType = MarginType::getByActionName($typeName);

        $recordModel = new self();
        $recordModel->user_id = $userId;
        $recordModel->uuid = $uuid;
        $recordModel->record_id = $recordId;
        $recordModel->type = $recordType['type'];
        $recordModel->type_to_cn = $recordType['type_to_cn'];
        $recordModel->amount = $amount;
        $recordModel->remark = $remark;
        $recordModel->is_affected_amount = $isAffectedAmount;
        if ($isAffectedAmount == self::NOT_AFFECTED_AVALIABLE) {
            if (is_null($status))
                $recordModel->status = $isAffectedAmount ? 200 : 400;
            else
                $recordModel->status = $status;
        } else {
            $recordModel->status = 200;
        }
        $recordModel->create_time = date('Y-m-d H:i:s');

        foreach ($amountData as $marginField => $marginAmount) {
            if (array_key_exists($marginField, $recordModel->tables[ $recordModel->tableName ]))
                $recordModel->setField($marginField, $marginAmount);
        }

        return $recordModel->save();
    }

    //获取用户所有交易流水
    public function getDetail($uuid, $type = null)
    {
        $records = $this->fields("'{$type}' as `type`, type_to_cn, amount, `status`, create_time", false)
            ->where("`uuid` = '{$uuid}'")
            ->get()->resultArr();
        foreach ($records as &$record) {
            $record['amount'] = number_format(abs($record['amount']), 2);
        }
        return $records;
    }

    /**
     * 获取资产余额与流水余额差异
     * @param $userId
     * @return bool
     */
    public function getMarginDiff($userId)
    {
        $recordMargin = '0.00';
        $records = $this->where(['user_id' => $userId])->get()->result();
        if ($records) {
            foreach ($records as $item) {
                if ('1' === $item->is_affected_amount)
                    $recordMargin = bcadd($recordMargin, $item->amount, 10);
            }
        }
        $marginModel = new MarginMargin($userId);

        return [
            'marginForMargin' => $marginModel->avaliable_amount,
            'marginForRecord' => $recordMargin,
        ];
    }

    /**
     * 根据流水校验余额
     * @param $userId
     * @return bool
     */
    public function checkMarginByRecord($userId)
    {
        $marginDiff = $this->getMarginDiff($userId);
        if (0 == bccomp($marginDiff['marginForMargin'], $marginDiff['marginForRecord'], 10))
            return true;
        else
            return false;
    }

}