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
}