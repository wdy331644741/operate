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

class AccountRpcImpl extends BaseRpcImpl
{

    const FROZEN_IDENTIFY = "identify";

    const RECORDS_ALL = 2;
    const RECORDS_IN = 1;
    const RECORDS_OUT = 0;

    const CHECK_IN_TYPE = 'checkin';

    protected $bankLogo;

    /**
     * 签到送体验金
     *
     * @JsonRpcMethod
     */
    public function checkIn()
    {
        //检查登录状态 null === false
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }
        $userId = $this->userId;
        $params = array(
            'user_id' => $userId,
        );
        //签到
        $message = Common::jsonRpcApiCall((object)$params, 'userSignIn', config('RPC_API.passport'));

        if (isset($message['result']) && count($message['result']) != 0) {
            return $message['result'];
        } else {
            throw new AllErrorException(AllErrorException::SAVE_CHECKIN_FAIL);
        }
    }

    /**
     * 获取用户的签到记录
     *
     * @JsonRpcMethod
     */
    public function userSignInMonth()
    {
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        $userId = $this->userId;

        //判断今天是否签到
        $postParamsCheckTodayUserSignIn = [
            "user_id" => $userId,
            "date"    => date('Y-m-d', time())
        ];
        $checkTodayUserSignIn = Common::jsonRpcApiCall((object)$postParamsCheckTodayUserSignIn, 'checkTodayUserSignIn', config('RPC_API.passport'));

        //签到
        $userSignInData = '';
        if ((isset($checkTodayUserSignIn['result']['status']) && !empty($checkTodayUserSignIn['result']['status'])) != true) {
            $paramsSignIn = array(
                'user_id' => $userId,
            );

            $userSignIn = Common::jsonRpcApiCall((object)$paramsSignIn, 'userSignIn', config('RPC_API.passport'));

            $userSignInData = (array)$userSignIn['result'];
        }

        $beginDate = date('Y-m-01', strtotime(date("Y-m-d")));
        $endDate = date('Y-m-d', strtotime("{$beginDate} +1 month"));

        $postParams = array(
            'user_id'    => $userId,
            'start_date' => $beginDate,
            'end_date'   => $endDate,
        );
        //获取所有本月签到时间
        $message = Common::jsonRpcApiCall((object)$postParams, 'userSignInMonth', config('RPC_API.passport'));

        //获取用户的连续签到情况
        $postParamsContinueDays = ['user_id' => $userId];
        $continueDays = Common::jsonRpcApiCall((object)$postParamsContinueDays, 'userSignInSuccessive', config('RPC_API.passport'));

        $continueDayNumber = (isset($continueDays['result']['continue_days']) && !empty($continueDays['result']['continue_days'])) ? $continueDays['result']['continue_days'] : 0;

        $year = date("Y", time());
        $month = date("m", time());
        $today = date("d", time());
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $gift = $this->getGift($continueDayNumber, $today);

        //处理签到情况
        $data = [];
        for ($i = 1; $i <= $days; $i++) {
            $key = $i - 1;
            $dateFormats = date('Y-m-d', strtotime("{$beginDate} +{$key} day"));
            $data[$key] = [
                'time'       => $dateFormats,
                'check_in'   => 0,
                'gift_check' => 0,
            ];
            if (count($message['result']) != 0) {
                foreach ($message['result'] as $value) {
                    if (date('Y-m-d', strtotime($value['create_time'])) == $dateFormats) {
                        $data[$key]['check_in'] = 1;
                    }
                }
            }
            foreach ($gift as $item) {
                if (intval($i) == intval($item['today'])) {
                    $data[$key]['gift_check'] = $item['type'] . '_' . (intval($item['day']) - $continueDayNumber);
                }
            }
        }

        $stringData = [];

        foreach ($data as $key => $item) {
            if (!empty($item['check_in'])) {
                $stringData[] = 1;
            } elseif (!empty($item['gift_check'])) {
                $stringData[] = $item['gift_check'];
            } else {
                $stringData[] = 0;
            }
        }
        
        $result = [
            'code'           => 200,
            'continueDays'   => empty($continueDayNumber) ? 0 : $continueDayNumber,
            'today'          => date("Y年m月d日", time()),
            'stringData'     => $stringData,
            'today_check'    => (isset($checkTodayUserSignIn['result']['status']) && !empty($checkTodayUserSignIn['result']['status'])) ? true : false,
            'userSignInData' => $userSignInData,
            'data'           => $data,
        ];

        return $result;
    }

    /**
     *
     * 在用户连续签到1-4次时，可以在页面中看到第5天可得奖励，当用户连续签到6天时，可以看到连续签到15天对应奖励.
     * 当用户连续签到5天，给用户0.1%加息券，加息时间2天
     * 当用户连续签到10天，给用户0.2%加息券，加息时间5天
     * 当用户连续签到20天，给用户0.3%加息券，加息时间5天
     * 后续每连续签到10天，给用户0.3%加息券，加息时间3天
     *
     * @param $day
     * @param $continueDays
     * @return bool
     */
    public function getGift($continueDays, $today)
    {
        $gift = [
            ['name' => '当用户连续签到5天，给用户0.1%加息券，加息时间2天', 'type' => 's1', 'day' => '5'],
            ['name' => '当用户连续签到10天，给用户0.2%加息券，加息时间5天', 'type' => 's2', 'day' => '10'],
            ['name' => '当用户连续签到20天，给用户0.3%加息券，加息时间5天', 'type' => 's3', 'day' => '20'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '30'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '40'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '50'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '60'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '70'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '80'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '90'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '100'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '110'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '120'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '130'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '140'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '150'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '160'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '170'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '180'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '190'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '200'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '210'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '220'],
            ['name' => '后续每连续签到10天，给用户0.3%加息券，加息时间3天', 'type' => 's4', 'day' => '230'],
        ];

        if (empty($continueDays)) {
            foreach ($gift as $key => $value) {
                $gift[$key]['today'] = intval($value['day']) - intval($continueDays) + intval($today) - 1;
            }
        } else {
            foreach ($gift as $key => $value) {
                $gift[$key]['today'] = intval($value['day']) - intval($continueDays) + intval($today);
            }
        }

        return $gift;
    }

    /**
     * 用户补签到
     *
     * @JsonRpcMethod
     */
    public function supplementUserSignIn($params)
    {
        if (($this->userId = $this->checkLoginStatus()) === false || empty($params->start_date)) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        $postParams = array(
            'user_id' => $params->user_id,
            'date'    => $params->date,
        );

        $message = Common::jsonRpcApiCall((object)$postParams, 'supplementUserSignIn', config('RPC_API.passport'));

        if (isset($message['result']) && count($message['result']) != 0) {
            return $message['result'];
        } else {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }
    }


    /**
     * 用户收益明细
     * @param $params
     * @return array
     * @throws AllErrorException
     *
     * @JsonRpcMethod
     */
    public function userProceedsDetailed()
    {
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        $userId = $this->userId;
        if (empty($userId)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }
        $configEarnings = new \Model\ConfigEarnings();

        $configEarningsInfo = $configEarnings->getInfoByTitle('revenueSharing');

        $startTime = $configEarningsInfo['start_time'];
        $endTime = $configEarningsInfo['end_time'];

        //获取累计获得体验金
        $postParams = array(
            'user_id'    => $userId,
            'start_time' => $startTime,
            'end_time'   => $endTime,
        );

        $amountExperience = Common::jsonRpcApiCall((object)$postParams, 'userExperienceGoldSum', config('RPC_API.passport'));

        //未投资好友/已投资好友
        $userInvestmentRecordPostParams = [
            'user_id' => $userId,
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ];
        $userInvestmentRecord = Common::jsonRpcApiCall((object)$userInvestmentRecordPostParams, 'userInvestmentRecord', config('RPC_API.passport'));

        $marketingRevenueSharing = new \Model\MarketingRevenueSharing();

        //0504修改不去产品 获取总资产（产品上带有利息）了改用  请求用户中心userInvestmentRecord 累加充值表
        $friendProperty = array();
        if($userInvestmentRecord['result']){
            $last_names = array_column($userInvestmentRecord['result'], 'id');
            //获取好友总资产
            $friendPropertyParams = [
                'friend_users' => $last_names
            ];
            $friendProperty = Common::jsonRpcApiCall((object)$friendPropertyParams, 'getUserAvaliableMargin', config('RPC_API.projects'));
        }
        // $friend_users = array();1
        // $unInvest_users = array();
        foreach ($userInvestmentRecord['result'] as $key => $value) {
            if ($value['recharge'] == true) {
                // $userInvestmentRecord['result'][$key]['invest'] = $userInvestmentRecord['result'][$key]['amount'];//0504修改好友总资产
                $userInvestmentRecord['result'][$key]['amount'] = $marketingRevenueSharing->getSumByUserId($value['id']);

                $userInvestmentRecord['result'][$key]['invest'] = empty($friendProperty['result']['data'][$value['id']])?0:$friendProperty['result']['data'][$value['id']];
            } else {
                $userInvestmentRecord['result'][$key]['invest'] = 0;//0504修改好友总资产，0510修改：尽管未投资 体验金会产生利息，也不展示
                $userInvestmentRecord['result'][$key]['amount'] = 0;
                // $userInvestmentRecord['result'][$key]['invest'] = empty($friendProperty['result']['data'][$value['id']])?0:$friendProperty['result']['data'][$value['id']];
                
            }
        }

        //获取累计获得收益
        $amount = $marketingRevenueSharing->getSumByUserIds(implode(',', array_column($userInvestmentRecord['result'], 'id')));
        //返回该用户的(推广员状态)
        $promoterModel = new \Model\PromoterList();
        $res = $promoterModel -> getPromoterInfoById($userId);
        // var_export($userId);
        // var_dump($res);exit;
        $res = empty($res[0]['status']) && $res[0]['status'] != '0'?-1:$res[0]['status'];
        return [
            'code'                   => 200,
            'experience_amount'      => $amountExperience['result']['count'],
            'revenue_sharing_amount' => $amount,
            'promoter_status'        => $res,
            'data'                   => $userInvestmentRecord['result']            
        ];
    }

    /**
     * 判断用户当天是否签到
     * @JsonRpcMethod
     */
    public function checkTodaySignIn()
    {
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        $userId = $this->userId;

        //获取累计获得体验金
        $postParams = array(
            'user_id' => $userId,
        );
        $checkTodaySignIn = Common::jsonRpcApiCall((object)$postParams, 'checkTodaySignIn', config('RPC_API.passport'));

        return ['code' => 0, 'status' => $checkTodaySignIn['result']['status'] ? 1 : 0];
    }

    /**
     * 获取活动接口配置信息
     * @JsonRpcMethod
     */
    public function getEarningsByTitle($params)
    {
        $title = $params->title;

        if (empty($title)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        $marketingRevenueSharing = new \Model\ConfigEarnings();
        $result = $marketingRevenueSharing->getInfoByTitle($title);
        return ['code'=>0,'data'=>$result];
    }

    /**
     * 申请成为推广员
     * @JsonRpcMethod
     */
    public function applyToPromoter(){

        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        $userId = $this->userId;
        //未投资好友/已投资好友
        $configEarnings = new \Model\ConfigEarnings();
        $configEarningsInfo = $configEarnings->getInfoByTitle('revenueSharing');
        $startTime = $configEarningsInfo['start_time'];
        $endTime = $configEarningsInfo['end_time'];

        $userInvestmentRecordPostParams = [
            'user_id' => $userId,
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ];
        $userInvestmentRecord = Common::jsonRpcApiCall((object)$userInvestmentRecordPostParams, 'userInvestmentRecord', config('RPC_API.passport'));
        //判断好友数量是否达到5个
        $friendNum = array_column($userInvestmentRecord['result'],'recharge');
        if(count($friendNum)  < 5 ){
            return ['code' => 2, 'data' => 'Error!'];
        }
        //获取好友总资产
        $last_names = array_column($userInvestmentRecord['result'], 'id');
        $friendPropertyParams = [
            'friend_users' => $last_names
        ];
        $friendProperty = Common::jsonRpcApiCall((object)$friendPropertyParams, 'getUserAvaliableMargin', config('RPC_API.projects'));
        $marketingRevenueSharing = new \Model\MarketingRevenueSharing();
        $inve_amount = 0;//所有好友投资总额
        foreach ($userInvestmentRecord['result'] as $key => $value) {
            if ($value['recharge'] == true) {
                $userInvestmentRecord['result'][$key]['amount'] = $marketingRevenueSharing->getSumByUserId($value['id']);
                //每个好友投资的钱
                $inve_amount += $friendProperty['result']['data'][$value['id']];
            } else {
                $userInvestmentRecord['result'][$key]['amount'] = 0;
                //$inve_amount += $friendProperty['result']['data'][$value['id']];
            }
        }
        //从所有好友那里 获取累计获得收益
        $amount = $marketingRevenueSharing->getSumByUserIds(implode(',', array_column($userInvestmentRecord['result'], 'id')));

        $promoterModel = new \Model\PromoterList();
        //查询该用户是否已存在
        if(!($promoterModel -> getPromoterInfoById($userId) )){
            //从用户中心获取用户基本信息
            $params = [
                'userId' => $userId,
            ];
            $userInfo = Common::jsonRpcApiCall((object)$params,'getUserBaseInfo',config('RPC_API.passport'));
            $PromoterParams = [
                'auth_id' => $userId,
                'username' => $userInfo['result']['realname'],
                'phone' => $userInfo['result']['phone'],
                'invite_num' => count($userInvestmentRecord['result']),  //邀请好友的数量
                'total_inve_amount' => $inve_amount,//好友投资总额
                'commission' => $amount,  //从好友 获取累计获得收益
                'create_time' => date('Y-m-d H:i:s',strtotime("-1 day")),
                'update_time' => date('Y-m-d H:i:s',strtotime("-1 day"))
            ];
            $result = $promoterModel -> addPromoter($PromoterParams);//增加推广员
        }else{
            return ['code' => 1, 'data' => 'Promoter exist'];
        }

        return ['code' => 0, 'data' => 'ApplySucceed'];
    }

    /**
     * 推广员龙虎榜
     * @JsonRpcMethod
     */
    public function winnersList(){

        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        $userId = $this->userId;

        $promoterModel = new \Model\PromoterList();
        $PromoterInfo  = $promoterModel -> getPromoterInfoById($userId);

        $status = empty($PromoterInfo)?-1:$PromoterInfo[0]['status'];

        $List = array(
                0 => array(
                        'show_phone' => "130****1717",
                        'earn'       => "1543.21",
                        ),
                1 => array(
                        'show_phone' => "130****1716",
                        'earn'       => "1142.64",
                        ),
                2 => array(
                        'show_phone' => "130****1745",
                        'earn'       => "852.64",
                        ),
                3 => array(
                        'show_phone' => "130****1745",
                        'earn'       => "852.64",
                        ),
                4 => array(
                        'show_phone' => "130****1745",
                        'earn'       => "852.64",
                        ),
                5 => array(
                        'show_phone' => "130****1745",
                        'earn'       => "852.64",
                        ),
                6 => array(
                        'show_phone' => "130****1745",
                        'earn'       => "852.64",
                        ),
                7 => array(
                        'show_phone' => "130****1745",
                        'earn'       => "852.64",
                        ),
                8 => array(
                        'show_phone' => "130****1745",
                        'earn'       => "852.64",
                        ),
                9 => array(
                        'show_phone' => "130****1745",
                        'earn'       => "852.64",
                        ),
                );
        return [
            'code' => 200,
            'Promoter' => $status,
            'data' => $List
        ];
    }

}
