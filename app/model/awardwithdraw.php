<?php
namespace Model;
class AwardWithdraw extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('award_withdraw');
        if ($pkVal)
            $this->initArData($pkVal);
    }


    public function filterUsefulWithdraw($nodeId)
    {
        $nowTime = date("Y-m-d H:i:s");
        $res = $this->where("`limit_node` = {$nodeId} and `effective_end` > '{$nowTime}' and status = 1 and is_del = 0")
            ->orderby("id DESC")
            ->get()->rowArr();
        
        return $res;
    }
}