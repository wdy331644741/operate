<?php
namespace Model;

class MarketingInterestcoupon extends Model {

    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_interestcoupon');
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
    public function couponInfo($couponId)
    {
        return $this->where("`id` = {$couponId}")->get()->rowArr();
    }

    //给用户添加记录
    public function addCouponForUser($userId, $awardInfo)
    {
        $data = array(
            'user_id'         => $userId,
            'uuid'            => create_guid(),
            'source_id'       => $awardInfo['id'],
            'source_name'     => $awardInfo['title'],
            'rate'            => $awardInfo['rate'],
            'effective_start' => date('Y-m-d H:i:s'),
            'effective_end'   => date('Y-m-d H:i:s', time() + $awardInfo['days'] * DAYS_SECONDS),
            'continuous_days' => $awardInfo['days'],
            'limit_desc'      => $awardInfo['limit_desc'],
            'create_time'     => date('Y-m-d H:i:s'),
            'update_time'     => date('Y-m-d H:i:s')
        );

        $res = $this->add($data);
        if ($res) {
            $data['id'] = $res;

            return $data;
        }

        return false;
    }

    //更新使用状态
    public function updateStatusOfUse($id)
    {
        return $this->where("`id` = {$id} and `is_use` = 0")
            ->upd(array('is_use' => 1, 'update_time' => date('Y-m-d H:i:s')));
    }
}