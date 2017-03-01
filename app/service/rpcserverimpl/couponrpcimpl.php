<?php


namespace App\service\rpcserverimpl;

use Lib\UserData;
use App\service\exception\AllErrorException;

/**
 * 用户加息券
 * Class CouponRpcImpl
 * @package App\service\rpcserverimpl
 */
class CouponRpcImpl extends BaseRpcImpl {

    /**
     * 可使用
     */
    const USABLE = 0;
    /**
     * 已使用
     */
    const USED = 1;

    /**
     * 入账和出账
     */
    const IN_ACCOUNT = 1;
    const OUT_ACCOUNT = 0;

    /**
     * 用户加息券列表
     *
     * @JsonRpcMethod
     */
    public function couponList()
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        $couponModel = new \Model\MarketingInterestcoupon();
        $couponList = $couponModel->getListByUserid($this->userId);
        $couponList = $this->sortCouponList($couponList);

        return array(
            'code'    => 0,
            'message' => 'success',
            'getMore'   => config("RPC_API.invite_page", ''),
            'data'    => $couponList
        );
    }

    /**
     * 用户加息券列表
     *
     * @JsonRpcMethod
     */
    public function useCoupon($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //验证
        if (empty($params->coupon_id)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }
        $coupon = new \Model\MarketingInterestcoupon();
        $couponInfo = $coupon->couponInfo($params->coupon_id);
        if (
            $couponInfo['user_id'] !== $this->userId ||
            !$this->couponIsActive($couponInfo) ||
            $couponInfo['effective_start'] > date("Y-m-d H:i:s")
        ) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL, [], '非法调用，加息券不可用');
        }

        $params = array(
            'user_id'               => $this->userId,
            'user_name'             => UserData::get('user_name'),
            'user_mobile'           => UserData::get('phone'),
            'token'                 => create_guid(),
            'interestcoupon_id'     => $couponInfo['id'],
            'interestcoupon_period' => $couponInfo['continuous_days'],
            'interestcoupon_rate'   => $couponInfo['rate']
        );
        $response = Common::jsonRpcApiCall(
            (object) $params, 'interestcouponBuy', config('RPC_API.projects')
        );
        if (isset($response['result']['code']) && $response['result']['code'] == 0) {
            //修改加息券状态
            $coupon->updateStatusOfUse($couponInfo['id']);

            return array(
                'code'    => 0,
                'message' => 'success'
            );
        }

        throw new AllErrorException(AllErrorException::API_ILLEGAL, [], '加息券使用失败');
    }

    /**
     * 用户体验金列表
     *
     * @JsonRpcMethod
     */
    public function experiences()
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        $experience = new \Model\MarketingExperience();
        //加息中的体验金总额
        $amount = $experience->getUseExperienceAmount($this->userId);

        //体验金列表
        $experienceRecords = $experience->getExperienceList($this->userId);
        $couponList = $this->sortAndBeauty($experienceRecords);

        return array(
            'code'      => 0,
            'message'   => 'success',
            'useAmount' => $amount,
            'getMore'   => config("RPC_API.invite_page", ''),
            'data'      => $couponList
        );
    }


    //加息券排序
    protected function sortCouponList($couponList)
    {
        if (empty($couponList)) {
            return array();
        }

        $activeList = array();
        $usedList = array();
        $expiredList = array();

        foreach ($couponList as $coupon) {
            $coupon['format_rate'] = $coupon['rate'] . '%';
            if ($this->couponIsActive($coupon)) {
                $coupon['status'] = 'active';
                $activeList[] = $coupon;
            } elseif ($coupon['is_use'] == self::USED) {
                $coupon['status'] = 'used';
                $usedList[] = $coupon;
            } else {
                $coupon['status'] = 'expired';
                $expiredList[] = $coupon;
            }
        }

        return array_merge($activeList, $usedList, $expiredList);
    }

    //加息券是否可用
    protected function couponIsActive($coupon)
    {
        return $coupon['is_use'] == self::USABLE
            && $coupon['effective_end'] > date("Y-m-d H:i:s");
    }

    //排序并修改字段
    protected function sortAndBeauty($records)
    {
        $records = $this->dispatchAndMerge($records);

        return array_orderby($records, 'datetime', SORT_DESC);
    }

    protected function dispatchAndMerge($records)
    {
        $inRecords = array();
        $outRecords = array();
        $nowDatetime = date("Y-m-d H:i:s");
        foreach ($records as $item) {
            $record['name'] = $item['source_name'];
            $record['amount'] = $item['amount'];
            //体验金已回收
            if ($nowDatetime > $item['effective_end']) {
                $record['type'] = self::OUT_ACCOUNT;
                $record['datetime'] = $item['effective_start'];
                $outRecords[] = $record;
                continue;
            }
            $record['type'] = self::IN_ACCOUNT;
            $record['datetime'] = $item['effective_start'];
            $inRecords[] = $record;

        }

        return array_merge($inRecords, $outRecords);
    }

}