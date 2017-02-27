<?php
namespace Model;

use App\service\rpcserverimpl\Common;

class AuthBankCard extends Model {

    //绑卡成功
    const BIND_SUCCESS = 1;

    public function __construct($pkVal = '')
    {
        parent::__construct('auth_bank_card');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    //添加绑卡关系根据绑卡请求记录数据
    public function addCardByReqLog($reqLog)
    {
        //是否绑定过
        $is_binded = $this->isBindCard($reqLog['user_id']);

        $cardData['user_id'] = $reqLog['user_id'];
        $cardData['bankcode'] = $reqLog['bankcode'];
        $cardData['bankname'] = $reqLog['bankname'];
        $cardData['channel'] = $reqLog['channel'];
        $cardData['realname'] = $reqLog['name'];
        $cardData['cardno'] = $reqLog['cardno'];
        $cardData['phone'] = $reqLog['phone'];
        $cardData['from_platform'] = getUAInfo('platform');
        $cardData['status'] = self::BIND_SUCCESS;
        $cardData['create_time'] = date("Y-m-d H:i:s");
        $cardData['update_time'] = date("Y-m-d H:i:s");

        $authCardInfo = $this->getBankCardInfo($cardData['user_id'], $cardData['cardno'], $cardData['channel']);

        //更新记录
        if (!empty($authCardInfo)) {
            $res = $this->update(
                array(
                    'phone'       => $reqLog['phone'],
                    'status'      => self::BIND_SUCCESS,
                    'update_time' => date("Y-m-d H:i:s")
                ),
                array('id' => $authCardInfo['id'])
            );
        } else {
            //新增
            $res = $this->add($cardData);
        }

        //首次绑卡
        if ($res && !$is_binded) {
            //广播消息
            Common::messageBroadcast('bind_bank_card', array(
                'user_id' => $reqLog['user_id'],
                'time'    => date("Y-m-d H:i:s")
            ));
        }

        //缓存失效
        invalidUserProfileCache($reqLog['user_id']);

        return $res;
    }

    //根据卡号获取绑卡信息
    public function getBankListByCard($cardno)
    {
        $status = self::BIND_SUCCESS;

        return $this->where("`cardno` = '{$cardno}' and `status` = {$status}")->get()->resultArr();
    }

    /**
     * 判断用户是否绑过卡
     *
     * @param $user_id
     *
     * @return bool
     */
    public function isBindCard($user_id)
    {
        $bankCardList = $this->getBindCardList($user_id);
        if (empty($bankCardList)) {
            return false;
        }

        return true;
    }

    /**
     * 获取用户绑定银行卡
     *
     * @param $user_id
     *
     * @return mixed
     */
    public function getBankCardInfo($user_id, $cardno, $channel = '')
    {
        if (empty($user_id) || empty($cardno)) {
            return false;
        }

        if (!empty($channel)) {
            $this->where("`channel` = '{$channel}'");
        }

        //单张卡信息
        return $this->where("`user_id` = {$user_id} and `cardno` = '{$cardno}'")
            ->get()->rowArr();
    }

    /**
     * 获取用户绑定银行卡信息
     *
     * @param $user_id
     * @param $card_no
     *
     * @return mixed
     */
    public function getBindCard($user_id, $card_no = '')
    {
        $status = self::BIND_SUCCESS;
        $where = "`user_id` = {$user_id} and `status` = {$status}";
        if (!empty($card_no)) {
            $where .= " and `cardno` = '{$card_no}'";
        }

        //银行卡列表
        return $this->where($where)->get()->rowArr();
    }

    /**
     * 获取用户绑卡成功列表(渠道去重)
     *
     * @param $userId
     *
     * @return array
     */
    public function getBindCardList($userId)
    {
        $bankList = $this->getAllBindCardList($userId);

        //去重
        return assoc_unique($bankList, 'cardno');
    }

    /**
     * 获取所有渠道的绑卡成功列表
     *
     * @param $userId
     *
     * @return array
     */
    public function getAllBindCardList($userId)
    {
        $status = self::BIND_SUCCESS;

        //银行卡列表
        $bankList = $this->where("`user_id` = {$userId} and `status` = {$status}")
            ->get()->resultArr();

        return $bankList;
    }

    /**
     * 获取用户绑卡数量
     *
     * @param $userId
     *
     * @return int
     */
    public function getBindCardNum($userId)
    {
        $where = array('user_id' => $userId, 'status' => self::BIND_SUCCESS);
        $result = $this->fields("count(id) as num", false)->where($where)->get()->rowArr();

        return (int) $result['num'];
    }
}