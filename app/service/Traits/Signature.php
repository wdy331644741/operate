<?php
namespace App\service\Traits;

trait Signature {

    protected $key = 'ed732d8d5204aa2d3fed97b8589e22e7';
    protected $signKey = 'ed732d8d_sign';

    //生成验签参数
    public function createSignature($params)
    {
        $isObject = is_object($params);
        $params = $isObject ? get_object_vars($params) : $params;

        $params[ $this->signKey ] = $this->getSignature($params);

        return $isObject ? (object) $params : $params;
    }

    //验证签名
    public function validSignature($params)
    {
        $params = is_object($params) ? get_object_vars($params) : $params;

        if (!isset($params[ $this->signKey ])) {
            return false;
        }
        $sign = $params[ $this->signKey ];

        return $sign == $this->getSignature($params);
    }

    private function getSignature($params)
    {
        if (isset($params[ $this->signKey ])) {
            unset($params[ $this->signKey ]);
        }
        ksort($params);
        $signStr = http_build_query($params) . "&key=".$this->key;

        return call_user_func('MD5', $signStr);
    }


}