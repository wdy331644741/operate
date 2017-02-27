<?php
namespace Model;
class AuthBankBindRequest extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('auth_bank_bind_request');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    //根据绑卡请求id获取请求记录
    public function getReqLogByReqId($reqid)
    {
        return $this->where("`requestid` = '{$reqid}'")->get()->rowArr();
    }
}