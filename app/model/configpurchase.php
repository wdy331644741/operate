<?php
namespace Model;
class ConfigPurchase extends Model
{
    const STATUS_INFINITE = 1;
    const STATUS_FINITE = 0;

    const INFINITE = 'infinite';

    public function __construct($pkVal = '')
    {
        parent::__construct('config_purchase');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function getLatestSetting()
    {
        return $this->orderby("id DESC")->get()->rowArr();
    }

}