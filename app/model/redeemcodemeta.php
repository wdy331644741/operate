<?php
namespace Model;
class RedeemCodeMeta extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('redeem_code_meta');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}