<?php
namespace Model;
class MarketingWithdrawcoupon extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_withdrawcoupon');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}