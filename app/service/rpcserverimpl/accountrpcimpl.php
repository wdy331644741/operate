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
     * 用户基础信息
     *
     * @JsonRpcMethod
     */
    public function profile()
    {
        //检查登录状态
//        if (($this->userId = $this->checkLoginStatus()) === false) {
//
//            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
//        }
        $this->userId = 19;
        $authUser = new \Model\AuthUser();
        $userInfo = $authUser->getUserBasicInfo($this->userId);

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $userInfo
        );
    }

    /**
     * 是否已经实名认证
     *
     * @JsonRpcMethod
     */
    public function isIdentify()
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //身份信息
        $authIdentify = new \Model\AuthIdentify();
        $identifyInfo = $authIdentify->getIdCardInfoByUserId($this->userId);

        if (empty($identifyInfo) || $identifyInfo['is_valid'] != 1) {
            $isIdentify = 0;
        } else {
            $isIdentify = 1;
        }

        return array(
            'code'        => 0,
            'message'     => 'success',
            'is_identify' => $isIdentify
        );
    }

    /**
     * 实名信息
     *
     * @JsonRpcMethod
     *
     * @return array
     * @throws AllErrorException
     */
    public function identifyInfo()
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //用户未实名
        if (($identifyInfo = Common::identifyChecked($this->userId)) === false) {
            throw new AllErrorException(AllErrorException::NOT_IDENTIFY);
        }

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => array(
                'name'         => $identifyInfo['name'],
                'id_number'    => $identifyInfo['id_number'],
                'valid_time'   => $identifyInfo['create_time'],
                'valid_client' => $identifyInfo['from_client']
            )
        );
    }

    /**
     * 交易记录
     *
     * @JsonRpcMethod
     */
    public function tradeRecords($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }
        //参数错误，非法调用
        if (
            !isset($params->type) ||
            !in_array($params->type, [self::RECORDS_ALL, self::RECORDS_IN, self::RECORDS_OUT])
        ) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL);
        }

        $tradeRecords = $this->getTradeRecordsByType($params->type);

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $tradeRecords
        );
    }

    /**
     * 签到送体验金
     *
     * @JsonRpcMethod
     */
    public function checkIn()
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }


        $params = array(
            'user_id' => $this->userId,
        );
        //使用体验金
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
    public function userSignInMonth($params)
    {

        if (($this->userId = $this->checkLoginStatus()) === false || empty($params->start_date)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        $postParams = array(
            'user_id'    => $params->user_id,
            'start_date' => $params->start_date,
            'end_date'   => $params->end_date,
        );

        $message = Common::jsonRpcApiCall((object)$postParams, 'userSignInMonth', config('RPC_API.passport'));

        if (isset($message['result']) && count($message['result']) != 0) {
            return $message['result'];
        } else {
            throw new AllErrorException(AllErrorException::SAVE_CHECKIN_FAIL);
        }

    }

    /**
     * 用户补签到
     *
     * @JsonRpcMethod
     */
    public function supplementUserSignIn($params)
    {
        if (($this->userId = $this->checkLoginStatus()) === false || empty($params->start_date)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
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




    /******************** ****************************/

    /**
     * 版本检查
     *
     * @JsonRpcMethod
     */
    public function versionChecked()
    {
        $platform = getUAInfo('platform');

        if ($platform == 'IOS') {
            return config("APP_VERSION.IOS", array());
        }

        if ($platform == 'ANDROID') {
            return config("APP_VERSION.ANDROID", array());
        }
    }

    /**
     * 触发节点，发放奖品
     * @param $nodeName
     * @return int 体验金金额
     */
    protected function sendAwardToUserIfExist($nodeName)
    {
        $awardNode = new \Model\AwardNode();
        $experience = new \Model\MarketingExperience();
        $awardExperience = new \Model\AwardExperience();

        $node = $awardNode->getNode($nodeName);

        if (!empty($node)) {
            $awardInfo = $awardExperience->filterUsefulExperience($node);
            $res = $experience->addExperienceForUser($this->userId, $awardInfo);
            $params = array(
                'user_id'           => UserData::get('user_id'),
                'user_name'         => UserData::get('user_name'),
                'user_mobile'       => UserData::get('phone'),
                'token'             => $res['uuid'],
                'experience_id'     => $res['id'],
                'experience_period' => $res['continuous_days'],
                'experience_amount' => $res['amount']
            );

            Common::jsonRpcApiCall((object)$params, 'experienceBuy', config('RPC_API.projects'));
            //修改体验金状态
            $experience->updateStatusOfUse($res['id']);

            return empty($res) ? 0 : $res['amount'];
        }

        return 0;
    }

    //根据类型获取所有交易记录
    protected function getTradeRecordsByType($type = 0)
    {
        $rechargeRecords = $this->getRechargeRecords($type);

        $withdrawRecords = $this->getWithdrawRecords($type);

        $records = $this->mergeAndSortsRecords($rechargeRecords, $withdrawRecords);

        //获取每笔买入和转出的详细进程（流水）
        return $this->getDetailForRecords($records);

    }

    //获取买入记录
    protected function getRechargeRecords($type)
    {
        //转出记录为空
        if ($type == self::RECORDS_OUT) {
            return array();
        }

        $recharge = new \Model\MarginRecharge();

        return $recharge->getUserRecords($this->userId);
    }

    //获取转出记录
    protected function getWithdrawRecords($type)
    {
        //转入记录为空
        if ($type == self::RECORDS_IN) {
            return array();
        }

        $withdraw = new \Model\MarginWithdraw();

        return $withdraw->getUserRecords($this->userId);
    }

    //合并买入、转出记录并排序
    protected function mergeAndSortsRecords($rechargeRecords, $withdrawRecords)
    {
        $allRecords = array_merge($rechargeRecords, $withdrawRecords);

        return array_orderby($allRecords, 'datetime', SORT_DESC);
    }

    protected function getDetailForRecords($records)
    {
        $marginRecords = new \Model\MarginRecord();

        foreach ($records as &$record) {

            $record['bank'] = $this->getBankItem($record['bank_account'], $record['bank_name']);
            $record['bank_logo'] = $this->getBankLogo($this->userId, $record['bank_account']);

            $detail = $marginRecords->getDetail($record['uuid'], $record['type']);

            $detail = $record['type'] == self::RECORDS_IN ?
                $this->addFakerDataForRecharge($detail) :
                $this->addFakerDataForWithdraw($detail);

            $record['detail'] = $detail;
            unset($record['bank_name'], $record['bank_account']);
        }

        return $records;
    }

    // return "招商银行(3433)"
    protected function getBankItem($cardNo, $bankName)
    {
        $lastCardNo = substr($cardNo, -4);

        return $bankName . "({$lastCardNo})";
    }

    protected function getBankLogo($userId, $cardNo)
    {
        $cardInfo = $this->getCardInfo($userId, $cardNo);
        if (!empty($cardInfo) && empty($this->bankLogo)) {
            $this->bankLogo = $cardInfo['bank_logo'];
        }

        return $this->bankLogo;
    }

    /**
     * 对应提现详情，增加受理项
     */
    protected function addFakerDataForRecharge($detail)
    {
        $faker = array(
            array(
                "type"        => 1,
                "type_to_cn"  => "买入已受理",
                "amount"      => $detail[0]['amount'],
                "status"      => "200",
                "create_time" => $detail[0]['create_time']
            ),
        );

        return array_merge($faker, $detail);
    }

    /**
     * 对应提现详情，增加受理项
     */
    protected function addFakerDataForWithdraw($detail)
    {
        if (count($detail) == 1 && $detail['0']['type_to_cn'] == '转出已受理') {
            $faker = array(
                array(
                    "type"        => 1,
                    "type_to_cn"  => "待审核",
                    "amount"      => $detail[0]['amount'],
                    "status"      => "100",
                    "create_time" => $detail[0]['create_time']
                ),
            );

            return array_merge($detail, $faker);
        }

        return $detail;
    }
}
