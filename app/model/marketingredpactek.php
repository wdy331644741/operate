<?php
namespace Model;
class MarketingRedpactek extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_redpactek');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}