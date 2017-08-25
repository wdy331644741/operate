<?php
namespace Model;
class SmsLog extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('sms_log');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}