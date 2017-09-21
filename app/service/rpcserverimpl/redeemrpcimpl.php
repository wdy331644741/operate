<?php

namespace App\service\rpcserverimpl;

use App\service\exception\AllErrorException;
use App\service\rpcserverimpl\SendCouponRpcImpl;
class RedeemRpcImpl extends BaseRpcImpl
{
    /**
     * 核销兑换码
     *
     * @JsonRpcMethod
     */

    public function verification($params)
    {

        if (($this->userId = $this->checkLoginStatus()) === false) {
            return ['code' => AllErrorException::ACCOUNT_TRADE_FROZEN,
                'message' => '用户未登录',

            ];
        }
        if (!preg_match('/\w{4,}/',$params->code)){
            return [
                'code'=>AllErrorException::VALID_CAPTCHA_FAIL,
                'message' => '兑换码格式错误',
            ];
        }

        $redeemModel = new  \Model\RedeemCode();
        $verifyRes = $redeemModel->verifyCode($this->userId, $params->code);

        if (!$verifyRes['is_ok']){
            return [
                    'code'=>AllErrorException::VALID_CAPTCHA_FAIL,
                    'message' => $verifyRes['msg'],
            ];
        }

        $redeemData = $verifyRes['redeem_data'];
        $status = $redeemModel->updateStatus($redeemData['code'], $this->userId);

        if (empty($status)){
            return [
                'code'=>AllErrorException::VALID_CAPTCHA_FAIL,
                'message' => '兑换失败',
            ];
        }
        $res = (new SendCouponRpcImpl)->sendAction($redeemData['type'],
            $this->userId, $redeemData['map_id']);
        if ($res['is_ok'] && $status ){
            return [
                'code' => 0,
                'type' => $redeemData['type'],
                'message' => $redeemData['prize_info'],
            ];
        }elseif(false == $res['is_ok']){
            return [
                'code' => AllErrorException::VALID_CAPTCHA_FAIL,
                'message' => $res['msg'],
            ];
        }

        return [
            'code'=>AllErrorException::VALID_CAPTCHA_FAIL,
            'message' => '兑换失败',
        ];

    }


}
