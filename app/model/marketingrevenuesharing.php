<?php
namespace Model;

class MarketingRevenueSharing extends Model
{

    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_revenuesharing');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    //获取用户所有加息券
    public function getListByUserid($userId)
    {
        return $this->fields('id, source_name, rate, effective_start, effective_end, continuous_days, is_use')
            ->orderby("rate desc, effective_end asc")
            ->where("`user_id` = {$userId}")
            ->get()->resultArr();
    }

    //获取加息券详情
    public function info($couponId)
    {
        return $this->where("`id` = {$couponId}")->get()->rowArr();
    }

    //给用户添加记录
    public function addRevenueSharing($awardInfo, $type)
    {
        $data = array(
            'type'        => $type,
            'user_id'     => $awardInfo['user_id'],
            'amount'      => $awardInfo['amount'],
            'start_time'  => $awardInfo['start_time'],
            'end_time'    => $awardInfo['end_time'],
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        );

        $result = $this->fields('id')
            ->orderby("id desc")
            ->where("`user_id` = '{$awardInfo["user_id"]}' and `start_time`='{$awardInfo["start_time"]}' and `end_time`= '{$awardInfo["end_time"]}'")
            ->get()->rowArr();

        if ($result) {
            $where = ['id' => $result['id']];
            $this->where($where)->upd(['amount' => $awardInfo['amount']]);
        } else {
            $res = $this->add($data);
            if ($res) {
                $data['id'] = $res;
                return $data;
            }
        }

        return false;
    }
}