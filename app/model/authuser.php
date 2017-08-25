<?php
namespace Model;

use Lib\UserData;

class AuthUser extends Model
{

    public function __construct($pkVal = '')
    {
        parent::__construct('auth_user');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    /**
     * 获取用户名称（实名取真实姓名，否则取手机号）
     * @param $userId
     *
     * @return bool
     */
    public function getUserName($userId, $mask = false)
    {
        $userInfo = $this->where("`id`={$userId}")->get()->rowArr();
        if (empty($userInfo)) {
            return false;
        }
        $phone = $mask ? mask_string($userInfo['phone'], 3, 6) : $userInfo['phone'];

        return empty($userInfo['realname']) ? $phone : $userInfo['realname'];
    }

    /**
     * 根据用户查询用户是否存在
     *
     * @param $username
     *
     * @return bool
     */
    public function getUserInfoByName($username)
    {
        $fields = "id, username, password, invite_code, from_channel, is_active";

        return $this->fields($fields)->where("`username` = '{$username}'")->get()->row();
    }

    /**
     * 通过邀请码获取用户id
     *
     * @param $code
     *
     * @return string
     */
    public function getUidByInviteCode($code)
    {
        $userInfo = $this->fields('id')->where("`invite_code` = '{$code}'")->get()->rowArr();
        if (empty($userInfo)) {
            return '';
        }

        return $userInfo['id'];
    }

    /**
     * 查询用户基本信息
     */
    public function getUserBasicInfo($id)
    {
        $redisInstanse = getReidsInstance();
        $cacheKey = 'userinfo' . $id;
        $userData = $redisInstanse->get($cacheKey);

        if (!$userData) {
            $userInfo = $this
                ->fields('id, username, phone, trade_pwd, realname, display_name, gender, birthday, email, last_login, last_ip, from_user_id, invite_code, from_channel, from_platform, is_active, create_time')
                ->where(array('id' => $id))->get()->row();

            if (UserData::get('last_login')) {
                $userInfo->last_login = UserData::get('last_login');
            }
            //获取实名状态
            $authIdentify = new AuthIdentify();
            $IdCardInfo = $authIdentify->getIdCardInfoByUserId($id);
            $userInfo->idcard_number = mask_string($IdCardInfo['id_number'], 6, -4);
            $userInfo->is_identify = !empty($IdCardInfo) && $IdCardInfo['is_valid'] == 1;

            //获取绑卡状态
            $bankCardModel = new AuthBankCard();
            $userInfo->is_bindcard = $bankCardModel->isBindCard($id);

            //是否设置交易密码
            $tradePwd = $userInfo->trade_pwd;
            $userInfo->isset_tradepwd = !empty($tradePwd);
            unset($userInfo->trade_pwd);

            //当月提现次数
            $withdraw = new MarginWithdraw();
            $userInfo->withdraw_num = (int) $withdraw->getFreeWithdrawNum($id);

            $redisInstanse->setex($cacheKey, 300, json_encode($userInfo));
        } else {
            $userInfo = json_decode($userData);
        }

        return $userInfo;
    }

    public function getInviteSourceUserId($userId)
    {
        $info = $this->where("`id` = {$userId}")->get()->rowArr();

        return $info['from_user_id'];
    }

    public function getInviteUserCounts($userId)
    {
        $result = $this->fields("count(*) as counts", false)->where("`from_user_id` = {$userId}")->get()->rowArr();
        return $result['counts'];
    }
}