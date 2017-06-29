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
        $redeemModel = new  \Model\RedeemCode();

        $verifyRes = $redeemModel->verifyCode($this->userId, $params->code);

        if (!$verifyRes['is_ok']){
            return [
                    'code'=>AllErrorException::VALID_CAPTCHA_FAIL,
                    'message' => $verifyRes['msg'],
            ];
        }

        $redeemData = $verifyRes['redeem_data'];

        $res = (new SendCouponRpcImpl)->sendAction($redeemData['type'],
            $this->userId, $redeemData['map_id']);
        if ($res){
            if ($redeemModel->updateStatus($params->code, $this->userId)){
                return [
                    'code' => 0,
                    'message' => '兑换成功',
                ];
            }

        }

        return [
            'code'=>AllErrorException::VALID_CAPTCHA_FAIL,
            'message' => '兑换失败',
        ];

    }


}
