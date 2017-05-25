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
}