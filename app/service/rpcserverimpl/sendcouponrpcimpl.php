<?php

namespace App\service\rpcserverimpl;

use App\service\exception\AllErrorException;
use App\service\rpcserverimpl\Common;

class SendCouponRpcImpl extends BaseRpcImpl
{


    // private $type = [
    //     1 => 'award_interestcoupon',
    //     2 => 'award_experience',
    //     3 => 'award_withdraw',
    //     4 => 'redPacket',

    // ];

    //手动发放奖品时打开注释
    public function activitySendAction($type,$userId,$nodeId){
        switch ($type){
            case 1:
                return $this->Acoupon($userId, $nodeId);
                break;
            case 2:
                return $this->Aexperience($userId, $nodeId);
                break;
            // case 3:
            //     return $this->AfreeWithdraw($userId, $nodeI);
            //     break;
            // case 4:
            //     return $this->Aredpacket($userId, $nodeI);
            //     break;
            default:
                break;
        }
    }
    /**
     * 根据类型发放不同奖品
     * @param $type
     * @param $userId
     * @param $nodeId
     * @return bool
     */
    public  function sendAction($type, $userId, $nodeId)
    {

        switch ($type){
            case 1:
                return $this->coupon($userId, $nodeId, $type);
                break;
            case 2:
                return $this->experience($userId, $nodeId, $type);
                break;
            case 3:
                return $this->freeWithdraw($userId, $nodeId,$type);
                break;
            default:
                break;
        }
    }
    /**
     * 发加息劵-针对手动发放的
     * @pageroute
     */
    private function coupon($userId,$nodeId,$type){

        $operateCoupon = new \Model\MarketingInterestcoupon();
        $redeemModel = new \Model\RedeemCode();
        $awardCouponInfo = $redeemModel->getPrizeInfo($nodeId,$type);
        if (empty($awardCouponInfo)) return ['is_ok' => false, 'msg'=>'奖品不可用'];

        $couponInfo = array(
            'id' => $awardCouponInfo['id'],
            'title' => $awardCouponInfo['title'],
            'rate' => $awardCouponInfo['rate'],
            'days' => $awardCouponInfo['days'],//加息券加息天数
            'effective_days' => $awardCouponInfo['effective_days'],//加息券有效天数
            'effective_start' => $awardCouponInfo['effective_start'],//加息券有效开始时间
            'effective_end' => $awardCouponInfo['effective_end'],//加息券有效结束时间
            'limit_desc' => $awardCouponInfo['limit_desc'],
            'is_use'     => 1
        );
        $addCouponRes = $operateCoupon->addCouponForUser($userId,$couponInfo);
        if (empty($addCouponRes)) return ['is_ok' => false, 'msg' => '添加记录失败'];
        //通知用户中心发放加息劵
        unset($addCouponRes['id']);
        $proPost = [
            'interestCoupon' => $addCouponRes
        ];
        $preRes = Common::jsonRpcApiCall((object)$proPost, 'preSendInterestCouponToUser', config('RPC_API.passport'));

        if ($preRes) {
            $activePost = [
                'uuid' => $addCouponRes['uuid'],
                'status' => 1,
                // 'immediately' => FALSE//立即使用 用户中心修改接口逻辑 不传immediately  不做操作直接返回ture
                // 'effective_start' =>  计息的开始时间
                // 'effective_end'   =>  计息的结束时间
            ];
            $rpcRes = Common::jsonRpcApiCall((object)$activePost, 'activateInterestCouponToUser', config('RPC_API.passport'));
        }

        $preRes = self::commCall(['interestCoupon' => $addCouponRes], 'preSendInterestCouponToUser');

        if (is_array($preRes)) return $preRes;

        $actRes = self::commCall([
            'uuid' => $addCouponRes['uuid'],
            'status' => 1,
            'immediately' => FALSE//立即使用
        ],'activateInterestCouponToUser');

        if (is_array($actRes)) return $actRes;

        $operateCoupon->updateActivate($addCouponRes['uuid']);

        return ['is_ok'=>true,'msg'=>''];

    }


    /**
     * 体验金
     */
    private function experience($userId,$nodeId,$type){
        $operateExperience = new \Model\MarketingExperience();
        $redeemModel = new \Model\RedeemCode();
        $awardExpInfo = $redeemModel->getPrizeInfo($nodeId, $type);

        if (empty($awardExpInfo)) return ['is_ok' => false, 'msg'=>'奖品不可用'];

        $amount = $awardExpInfo['amount'];
        if ($awardExpInfo['amount_type']==1){
            $amount = rand($awardExpInfo['min_amount'],$awardExpInfo['max_amount']);
        }

        $experienceInfo = array(
            'id' 	     => $awardExpInfo['id'],
            'title'      => $awardExpInfo['title'],
            'amount'     => $amount,
            'days'       => $awardExpInfo['days'],//10天后有效 +5天使用时间
            'limit_desc' => $awardExpInfo['limit_desc'],
            'amount_type'=> $awardExpInfo['amount_type'],
            'is_use'     => 1
        );

        $addExperienceRes = $operateExperience->addExperienceForUser($userId,$experienceInfo);
        if (empty($addExperienceRes)) return ['is_ok' => false, 'msg'=>'添加记录失败'];
        //后使用
        $expId = $addExperienceRes['id'];
        unset($addExperienceRes['id']);
        //通知用户中心 预发放体验金
        $preRes = self::commCall(['expAward' => $addExperienceRes], 'preSendExperienceGoldToUser');
        if (is_array($preRes)) return $preRes;

        $actRes = self::commCall([
            'uuid' => $addExperienceRes['uuid'],
            'status' => 1,
        ], 'activateExperienceGoldToUser');

        if (is_array($actRes)) return $actRes;

        $operateExperience->updateStatusOfUse($expId);

        return ['is_ok'=>true, 'msg'=>''];

    }

    /**
     * 发提现劵
     */
    private function freeWithdraw($userId,$nodeId,$type){
        $redeemModel = new \Model\RedeemCode();
        $awardWithdrawInfo = $redeemModel->getPrizeInfo($nodeId,$type);
        $FreeWithdraw = new \Model\MarketingWithdrawcoupon();

        if (empty($awardWithdrawInfo)) return ['is_ok' => false, 'msg'=>'奖品不可用'];

        $withdrawInfo = array(
            'id' => $awardWithdrawInfo['id'],
            'title' => $awardWithdrawInfo['title'],
            // 'remain_times' => $awardWithdrawInfo['times'],
            'effective_end' => $awardWithdrawInfo['effective_end'],
            'limit_desc' => $awardWithdrawInfo['limit_desc'],
        );

        $addWithdrawRes = $FreeWithdraw->addWithdrawForUser($userId,$withdrawInfo);
        $upId = $addWithdrawRes['id'];
        unset($addWithdrawRes['id']);
        //通知用户中心 发放提现劵
        if (empty($addWithdrawRes)) return ['is_ok' => false, 'msg'=>'添加记录失败'];

        $preRes = self::commCall(['withdrawCoupon'   => $addWithdrawRes], 'preSendWithdrawCouponToUser');

        if (is_array($preRes)) return $preRes;
        $actRes = self::commCall([
            'uuid' => $addWithdrawRes['uuid'],
            'status' => 1,
        ], 'activateWithdrawCouponToUser');

        if (is_array($actRes)) return $actRes;

        $FreeWithdraw->updateStatusOfUse($upId);
        return ['is_ok' => true, 'msg'=>''];
    }

    private static function commCall($postData,$method)
    {
        try {
            Common::jsonRpcApiCall((object)$postData, $method, config('RPC_API.passport'));
        } catch (\Exception $e){
            return ['is_ok' => false, 'msg' => $e->getMessage()];
        }

        return true;
    }


    private function Acoupon($userId,$nodeId){
        $operateCoupon = new \Model\MarketingInterestcoupon();
        $awardCouponModel = new \Model\AwardInterestcoupon();
        $awardCouponInfo = $awardCouponModel->filterUsefulInterestCoupon($nodeId);

        //*********************发放加息劵*********************
        $couponInfo = array(
            'id' => $awardCouponInfo['id'],
            'title' => $awardCouponInfo['title'],
            'rate' => $awardCouponInfo['rate'],
            'days' => $awardCouponInfo['days'],//加息券加息天数
            'effective_days' => $awardCouponInfo['effective_days'],//加息券有效天数
            'effective_start' => $awardCouponInfo['effective_start'],//加息券有效开始时间
            'effective_end' => $awardCouponInfo['effective_end'],//加息券有效结束时间
            'limit_desc' => $awardCouponInfo['limit_desc'],
            'is_use'     => 0,//is_use 默认为0
        );
        $addCouponRes = $operateCoupon -> addCouponForUser($userId,$couponInfo);
        //***************************************************
        //通知用户中心发放加息劵  预发放
        unset($addCouponRes['id']);
        $proPost = [
            'interestCoupon' => $addCouponRes
        ];
        $preRes = Common::jsonRpcApiCall((object)$proPost, 'preSendInterestCouponToUser', config('RPC_API.passport'));

        if ($preRes) {
            $activePost = [
                'uuid' => $addCouponRes['uuid'],
                'status' => 1,
                'immediately' => 2//立即使用 用户中心修改接口逻辑 不传immediately  不做操作直接返回ture
                // 'effective_start' =>  计息的开始时间
                // 'effective_end'   =>  计息的结束时间
            ];
            $rpcRes = Common::jsonRpcApiCall((object)$activePost, 'activateInterestCouponToUser', config('RPC_API.passport'));
        }

        //update operate database  is_use status
        if($rpcRes) {
            $operateCoupon->updateIsuse($addCouponRes['uuid']);
        }

        if (!$preRes || !$rpcRes || !$addCouponRes){
            return false;
        }
        return true;
    }

    private function Aexperience($userId,$nodeId){

        $operateExperience = new \Model\MarketingExperience();
        $awardExpModel = new \Model\AwardExperience();
        $awardExpInfo = $awardExpModel->filterUsefulInterestCoupon($nodeId);

        $amount = $awardExpInfo['amount'];
        if ($awardExpInfo['amount_type']==1){
            $amount = rand($awardExpInfo['min_amount'],$awardExpInfo['max_amount']);
        }

        //***************发放体验金************************
        $experienceInfo = array(
            'id'         => $awardExpInfo['id'],
            'title'      => $awardExpInfo['title'],
            'amount'     => $amount,
            'days'       => $awardExpInfo['days'],//10天后有效 +5天使用时间
            'limit_desc' => $awardExpInfo['limit_desc'],
            'amount_type'=> $awardExpInfo['amount_type'],
            'is_use'     => 1
        );
        $addExperienceRes = $operateExperience -> addExperienceForUser($userId,$experienceInfo);
        //后使用
        $expId = $addExperienceRes['id'];
        unset($addExperienceRes['id']);
        //通知用户中心 预发放体验金
        if($addExperienceRes){
            $activePost = array(
                'expAward'   => $addExperienceRes,
            );
            $resRpc = Common::jsonRpcApiCall((object)$activePost, 'preSendExperienceGoldToUser', config('RPC_API.passport'));
        }

        $activePost = [
            'uuid' => $addExperienceRes['uuid'],
            'status' => 1,

        ];
        $rpcRes = Common::jsonRpcApiCall((object)$activePost, 'activateExperienceGoldToUser', config('RPC_API.passport'));

        if($rpcRes){
            $operateExperience->updateStatusOfUse($expId);
        }
        if (!$resRpc || !$rpcRes || !$addExperienceRes){
            return false;
        }

        return true;
    }

}
