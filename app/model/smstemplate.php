<?php
namespace Model;
class SmsTemplate extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('sms_template');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    //根据节点获取模版
    public function getTmplByNode($nodeName)
    {
        return $this->where("`node_name` = '{$nodeName}' and status = 1")
            ->get()->rowArr();
    }

}