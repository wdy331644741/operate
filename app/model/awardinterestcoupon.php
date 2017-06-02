<?php
namespace Model;
class AwardInterestcoupon extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('award_interestcoupon');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function filterUsefulInterestCoupon($nodeId)
    {
        $nowTime = date("Y-m-d H:i:s");

        return $this->where("`limit_node` = {$nodeId} and `effective_end` > '{$nowTime}' and status = 1 and is_del = 0")
            ->orderby("id DESC")
            ->get()->rowArr();
    }

    public function getCouponIdByName($coupon){
        return $this->where("`coupon` = '{$coupon}'")
            ->get()->rowArr();

    }

    public function filterUsefulInterestCouponNotime($nodeId){
        return $this->where()
            ->orderby("id DESC")
            ->get()->rowArr();
    }
}