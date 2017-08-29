<?php
namespace Model;
class AwardInterestcoupon extends Model
{   
    const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;
    public function __construct($pkVal = '')
    {
        parent::__construct('award_interestcoupon');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function getCouponIdByName($coupon,$noDate = false){
        $nowTime = date("Y-m-d H:i:s");
        if($noDate){
            return $this->where("`coupon` = '{$coupon}' and status = 1 and is_del = 0")
            ->get()->rowArr();
        }else{
            return $this->where("`coupon` = '{$coupon}' and `effective_end` > '{$nowTime}' and status = 1 and is_del = 0")
            ->get()->rowArr();
        }
    }

    public function filterUsefulInterestCouponNotime($nodeId){
        return $this->where("`limit_node` = {$nodeId} and status = 1 and is_del = 0")
            ->orderby("id DESC")
            ->get()->rowArr();
        if(is_array($nodeId)){
            $nodeIdStr = implode(',', $nodeId);
            return $this->where("`limit_node` in ({$nodeIdStr}) and `effective_end` > '{$nowTime}' and status = 1 and is_del = 0")
                ->orderby("id DESC")
                ->get()->resultArr();
        }else{
            return $this->where("`limit_node` = {$nodeId} and `effective_end` > '{$nowTime}' and status = 1 and is_del = 0")
                ->orderby("id DESC")
                ->get()->rowArr();
        }
    }
    public function filterUsefulInterestCoupon($nodeId)
    {
        $nowTime = date("Y-m-d H:i:s");
        if(is_array($nodeId)){
            $nodeIdStr = implode(',', $nodeId);
            return $this->where("`limit_node` in ({$nodeIdStr}) and `effective_end` > '{$nowTime}' and status = 1 and is_del = 0")
                ->orderby("id DESC")
                ->get()->resultArr();
        }else{
            return $this->where("`limit_node` = {$nodeId} and `effective_end` > '{$nowTime}' and status = 1 and is_del = 0")
                ->orderby("id DESC")
                ->get()->rowArr();
        }
        
    }

    public function switchStausById($id)
    {
        $where = ['id' => $id];
        $row = $this->where($where)->get()->row();
        if ($row->status == self::STATUS_TRUE)
            $status = self::STATUS_FALSE;
        else
            $status = self::STATUS_TRUE;
        return $this->where($where)->upd(['status' => $status]);
    }
    
}