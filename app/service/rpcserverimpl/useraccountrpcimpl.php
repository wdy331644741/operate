<?php
/**
 * Author     : newiep.
 * CreateTime : 19:26
 * Description: 安全相关接口服务
 */

namespace App\service\rpcserverimpl;

use App\service\exception\AllErrorException;
use Lib\UserData;
use Model\AuthUser;
use Model\Model;
use Model\ReportFootprint;

class UserAccountRpcImpl extends BaseRpcImpl
{
	/**
     * app邀请有奖
     * @JsonRpcMethod
     */
	public function invitePrize(){
		//检查登录状态 null === false
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        $params = array(
            'userId' => $this->userId,
        );
        $promoterModel = new \Model\PromoterList();
        $earnings = $promoterModel->getToBePromoter($this->userId)? 1:0;
        $message = Common::jsonRpcApiCall((object)$params, 'getUserMarginInfo', config('RPC_API.passport'));

		$commission = $message['result']['refund']['amount'] + $message['result']['refund']['interest'] + $message['result']['refund']['exp_interest'];
        $newArray = array();
        $tmpMargin = array_column($message['result']['margin'],'avaliable_amount','user_id');

        foreach ($message['result']['userId'] as $key => $value) {
        	$newArray[$key] = array(
        			'display_name' => $value['display_name'], 
                    'avaliable_amount' => $tmpMargin[$value['id']], //总资产
        			'recharge' => 1, //是否投资
        		);
        }
		return ['code' => 200,'message' => '返回成功','data' => array('relation' =>$newArray),'earnings' => $earnings,'commission' => $commission ];
	}

}