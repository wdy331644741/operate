<?php
/**
 * Author     : newiep.
 * CreateTime : 19:26
 * Description: 安全相关接口服务
 */

namespace App\service\rpcserverimpl;

use Lib\MqClient;
use App\service\Traits\Validator;
use App\service\exception\AllErrorException;

class SecureRpcImpl extends BaseRpcImpl
{
    use Validator;

    //交易密码冻结标识
    const FROZEN_TRADE_PWD = "trade_pwd";

    //登录密码冻结标识
    const FROZEN_SIGNIN_PWD = "login";

    //找回登录密码短信、语音验证码session标识
    const RETRIEVE_SMS_FLAG = 'session_retrieve_sms';

    //修改绑定手机短信、语音验证码session标识
    const MODIFY_PHONE_SMS = "session_modify_phone_sms";

    //身份检查操作标识
    const CHECKED_IDENTIFY = "handle_checked_identify";

    /**
     * 当前用户id
     *
     * @var
     */
    protected $userId;

    /**
     * 修改登录密码
     *
     * @JsonRpcMethod
     *
     * @param $params
     *
     * @return string
     * @throws AllErrorException
     */
    public function modifySigninPwd($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //接口必要参数
        if (!isset($params->old_password) || !isset($params->new_password)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        //登录密码验证接口
        $params->password = $params->old_password; //构造接口请求参数
        Common::localApiCall($params, "checkSigninPwd", "SecureRpcImpl");

        $userModel = new \Model\AuthUser($this->userId);

        //检查新密码的格式
        if (strlen($params->new_password) < 6) {
            throw new AllErrorException(AllErrorException::VALID_PWD_FAIL);
        }

        //修改密码
        $userModel->password = makePassword($params->new_password);
        if ($userModel->save() !== false) {

            //广播消息
            $mq_client = new MqClient();
            $mq_client->send('modify_login_pwd', array(
                'user_id' => $userModel->id,
                'time' => date("Y-m-d H:i:s"),
                'ip' => get_client_ip()
            ));

            //删除登录状态
            Common::localApiCall([], 'signout', 'AuthorizeRpcImpl');

            return array(
                'code' => 0,
                'message' => '密码修改成功'
            );
        }
        throw new AllErrorException(AllErrorException::SAVE_SIGNIN_PWD_FAIL);
    }

    /**
     * 找回登录密码
     *
     * @JsonRpcMethod
     *
     * @param $params
     *
     * @return array
     * @throws AllErrorException
     */
    public function retrieveSigninPwd($params)
    {
        if (empty($params->sms_key)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '请先获取短信验证码');
        }

        if (empty($params->sms_code)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '短信验证码不为空');
        }

        //接口必要参数
        if (!isset($params->mobile)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        //短信验证码验证
        if (Captcha::valid($params->sms_key, $params->sms_code, $params->mobile, self::RETRIEVE_SMS_FLAG)) {
            //验证用户名（手机号）
            $username = Common::checkMobileAndEnc($params->mobile);
            $userModel = new \Model\AuthUser();
            $user_info = $userModel->getUserInfoByName($username);
            $userModel->initArData($user_info->id);

            //检查密码
            if (empty($params->password) || strlen($params->password) < 6) {
                throw new AllErrorException(AllErrorException::VALID_PWD_FAIL);
            }
            //修改密码
            $userModel->password = makePassword($params->password);

            if ($userModel->save() !== false) {

                //清空错误记录
                $this->resetRatethrottle($this->userId, self::FROZEN_SIGNIN_PWD);

                //删除登录状态
                Common::localApiCall([], 'signout', 'AuthorizeRpcImpl');

                //修改密码成功
                return array(
                    'code' => 0,
                    'message' => '新密码修改成功'
                );
            }
            throw new AllErrorException(AllErrorException::SAVE_SIGNIN_PWD_FAIL);
        }
        //错误提示
        throw new AllErrorException(AllErrorException::VALID_SMS_FAIL);
    }

    /**
     * 找回密码发送短信、语音验证码
     *
     * @JsonRpcMethod
     *
     * @param $params
     *
     * @return array
     * @throws AllErrorException
     */
    public function retrieveSmsCode($params)
    {
        if (empty($params->img_key) && empty($params->challenge)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '请先获取图片验证码');
        }

        //接口必要参数
        if (empty($params->mobile)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '手机号不为空');
        }

        //验证用户名（手机号）
        $username = Common::checkMobileAndEnc($params->mobile);
        if ($username === false) {
            throw new AllErrorException(AllErrorException::VALID_PHONE_FAIL);
        }

        //验证用户名是否存在
        if (Common::checkMobileIsUsed($username) === false) {
            throw new AllErrorException(AllErrorException::USERNAME_NOT_EXIST);
        }

        //验证图片或极验(验证后不刷新码值)
        $this->validCaptcha($params, false);

        //发送短信 || 语音验证码
        $sms = isset($params->send_voice) && $params->send_voice == 1 ? false : true;

        //发送短信或语音
        $smsHandle = new Captcha($params->mobile, self::RETRIEVE_SMS_FLAG);
        $data = $smsHandle->send('change_pass_code', $sms);
        $accessLog = $smsHandle->getAccessLog($sms);

        return array(
            'code' => 0,
            'message' => 'success',
            'data' => array(
                'key' => $data['key'],
                'call_num' => $accessLog['counter']
            )
        );
    }

    /**
     * 设置交易密码
     *
     * @JsonRpcMethod
     */
    public function tradePwdSet($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }
        //用户信息
        $authUserModel = new \Model\AuthUser($this->userId);
        if ($authUserModel->trade_pwd) {
            //已经设置交易密码
            throw new AllErrorException(AllErrorException::HAD_SET_TRADE_PWD);
        }

        //交易密码格式（6位，只能包含数字）
        if (!$this->validateTradePwd($params->trade_pwd)) {
            throw new AllErrorException(AllErrorException::VALID_PWD_FAIL);
        }

        //保存交易密码
        $authUserModel->trade_pwd = makePassword($params->trade_pwd);

        if ($authUserModel->save()) {
            //用户数据缓存失效
            invalidUserProfileCache($this->userId);

            return array(
                'code' => 0,
                'message' => '设置成功'
            );
        }
        throw new AccountErrorException(AllErrorException::SAVE_TRADE_PWD_FAIL);
    }

    /**
     * 修改交易密码--记得旧密码
     *
     * @JsonRpcMethod
     */
    public function modifyTradePwdModeA($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //接口必要参数
        if (empty($params->old_trade_pwd) || empty($params->new_trade_pwd)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '密码不为空');
        }

        //交易密码验证接口
        $params->trade_pwd = $params->old_trade_pwd; //构造接口请求参数
        Common::localApiCall($params, "checkTradePwd", "SecureRpcImpl");

        //交易密码格式（6位，只能包含数字）
        if (!$this->validateTradePwd($params->new_trade_pwd)) {
            throw new AllErrorException(AllErrorException::VALID_PWD_FAIL);
        }

        //更新交易密码
        $authUserModel = new \Model\AuthUser($this->userId);
        $authUserModel->trade_pwd = makePassword($params->new_trade_pwd);

        $result = $authUserModel->save();

        if ($result) {
            //清空交易密码冻结次数
            $this->resetRatethrottle($this->userId, self::FROZEN_TRADE_PWD);
            //用户数据缓存失效
            invalidUserProfileCache($this->userId);

            return array(
                'code' => 0,
                'message' => '修改交易密码成功'
            );
        }
        throw new AllErrorException(AllErrorException::SAVE_TRADE_PWD_FAIL);

    }

    /**
     * 修改交易密码--身份认证方式
     *
     * @JsonRpcMethod
     */
    public function modifyTradePwdModeB($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //接口必要参数
        if (empty($params->cardno)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '银行卡号不为空');
        }

        if (empty($params->idcardno)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '身份证号不为空');
        }

        //检查是否实名
        if (($IdCardInfo = Common::identifyChecked($this->userId)) === false) {
            throw new AllErrorException(AllErrorException::NOT_IDENTIFY);
        }
        //身份证号是否一致
        if ($IdCardInfo['id_number'] != $params->idcardno) {
            throw new AllErrorException(AllErrorException::INPUT_IDCARD_ERROR);
        }

        //检查用户绑卡信息
        $bankCardModel = new \Model\AuthBankCard();
        $bankCard = $bankCardModel->getBindCard($this->userId, $params->cardno);

        if (empty($bankCard)) {
            throw new AllErrorException(AllErrorException::NOT_BIND_BANKCARD, [], '身份校验失败');
        }

        return array(
            'code' => 0,
            'message' => 'success',
            'data' => $this->getHandleToken(self::CHECKED_IDENTIFY)
        );
    }

    /**
     * 修改交易密码--身份认证方式forPc
     *
     * @JsonRpcMethod
     */
    public function modifyTradePwdForPc($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //接口必要参数
        if (empty($params->cardno)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '银行卡号不为空');
        }

        if (empty($params->idcardno)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '身份证号不为空');
        }

        if (empty($params->trade_pwd)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '交易密码不为空');
        }

        //检查是否实名
        if (($IdCardInfo = Common::identifyChecked($this->userId)) === false) {
            throw new AllErrorException(AllErrorException::NOT_IDENTIFY);
        }
        //身份证号是否一致
        if ($IdCardInfo['id_number'] != $params->idcardno) {
            throw new AllErrorException(AllErrorException::INPUT_IDCARD_ERROR);
        }

        //检查用户绑卡信息
        $bankCardModel = new \Model\AuthBankCard();
        $bankCard = $bankCardModel->getBindCard($this->userId, $params->cardno);

        if (empty($bankCard)) {
            throw new AllErrorException(
                AllErrorException::NOT_BIND_BANKCARD, [], '此卡未绑定'
            );
        }

        //更新交易密码
        $authUserModel = new \Model\AuthUser($this->userId);
        $authUserModel->trade_pwd = makePassword($params->trade_pwd);

        $result = $authUserModel->save();

        if ($result) {
            //清空交易密码冻结次数
            $this->resetRatethrottle($this->userId, self::FROZEN_TRADE_PWD);
            //用户数据缓存失效
            invalidUserProfileCache($this->userId);

            return array(
                'code' => 0,
                'message' => '修改交易密码成功'
            );
        }
        throw new AllErrorException(AllErrorException::SAVE_TRADE_PWD_FAIL);
    }

    /**
     * 更新交易密码
     *
     * @JsonRpcMethod
     */
    public function updateTradePwd($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //接口必要参数
        if (!isset($params->handle) || !isset($params->token)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }
        if (empty($params->trade_pwd)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '交易密码不为空');
        }

        //交易密码格式（6位，只能包含数字）
        if (!$this->validateTradePwd($params->trade_pwd)) {
            throw new AllErrorException(AllErrorException::VALID_PWD_FAIL);
        }

        //验证handle token
        if (!$this->validHandleToken($params->handle, $params->token)) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL);
        }

        //更新交易密码
        $authUserModel = new \Model\AuthUser($this->userId);

        //读取用户交易密码信息
        $tradePwd = $authUserModel->trade_pwd;
        if (empty($tradePwd)) {
            throw new AllErrorException(AllErrorException::NOT_SET_TRADE_PWD);
        }
        //更新交易密码
        $authUserModel->trade_pwd = makePassword($params->trade_pwd);

        if ($authUserModel->save()) {
            //清空交易密码冻结次数
            $this->resetRatethrottle($this->userId, self::FROZEN_TRADE_PWD);
            //用户数据缓存失效
            invalidUserProfileCache($this->userId);

            return array(
                'code' => 0,
                'message' => '交易密码修改成功'
            );
        }
        throw new AllErrorException(AllErrorException::SAVE_TRADE_PWD_FAIL);
    }

    /**
     * 验证交易密码
     *
     * @JsonRpcMethod
     */
    public function checkTradePwd($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //接口必要参数
        if (empty($params->trade_pwd)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '交易密码不为空');
        }

        //交易密码格式（6位，只能包含数字）
        if (!$this->validateTradePwd($params->trade_pwd)) {
            throw new AllErrorException(AllErrorException::VALID_PWD_FAIL);
        }

        //检查账号交易是否冻结
        if ($this->frozenChecked($this->userId, self::FROZEN_TRADE_PWD)) {
            throw new AllErrorException(AllErrorException::ACCOUNT_TRADE_FROZEN);
        }

        //读取用户交易密码信息
        $authUserModel = new \Model\AuthUser($this->userId);
        $tradePwd = $authUserModel->trade_pwd;
        if (empty($tradePwd)) {
            throw new AllErrorException(AllErrorException::NOT_SET_TRADE_PWD);
        }

        //检查交易密码
        if (!checkPassword($tradePwd, $params->trade_pwd)) {
            $frozenConf = config("FROZEN_CONFIG." . self::FROZEN_TRADE_PWD);
            //记录错误次数
            $failedNum = $this->incrementRatethrottle($this->userId, self::FROZEN_TRADE_PWD);

            //剩余尝试次数
            $tryNum = $frozenConf['trigger_num'] - $failedNum;
            if ($tryNum <= 0) {
                throw new AllErrorException(AllErrorException::ACCOUNT_TRADE_FROZEN);
            } else {
                //自定义错误
                $errorMsg = "交易密码错误,您还可以输入{$tryNum}次";
                throw new AllErrorException(
                    AllErrorException::INPUT_PWD_ERROR,
                    array("error_num" => $failedNum),
                    $errorMsg
                );
            }
        } else {
            //清空错误记录
            $this->resetRatethrottle($this->userId, self::FROZEN_TRADE_PWD);

            return array(
                'code' => 0,
                'message' => 'success'
            );
        }
    }

    /**
     * 解冻交易密码
     *
     * @JsonRpcMethod
     */
    public function refrozeTradePwd()
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //检查账号交易是否冻结
        if ($this->frozenChecked($this->userId, self::FROZEN_TRADE_PWD)) {
            //清空错误记录
            $this->resetRatethrottle($this->userId, self::FROZEN_TRADE_PWD);
        }

        return array(
            'code' => 0,
            'message' => 'success'
        );
    }

    /**
     * 修改绑定手机
     *
     * @JsonRpcMethod
     */
    public function modify_phone($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //短信验证码验证
        if (Captcha::valid($params->sms_key, $params->sms_code, $params->newphone, self::MODIFY_PHONE_SMS)) {

            $authUserModel = new \Model\AuthUser($this->userId);
            $bankCardModel = new \Model\AuthBankCard();
            $authIdentify = new \Model\AuthIdentify();

            //身份信息
            $IdCardInfo = $authIdentify->getIdCardInfoByUserId($this->userId);

            //验证登录密码是否正确
            if (!checkPassword($authUserModel->password, $params->password)) {
                throw new AllErrorException(AllErrorException::INPUT_PWD_ERROR);
            }
            //验证身份证是否正确
            if (empty($IdCardInfo) ||
                $IdCardInfo['is_valid'] != 1 ||
                $IdCardInfo['id_number'] != $params->idcard
            ) {
                throw new AllErrorException(AllErrorException::NOT_IDENTIFY);
            }

            //验证银行卡
            $bankCard = $bankCardModel->getBindCard($this->userId, $params->bankcard);
            if (empty($bankCard)) {
                throw new AllErrorException(AllErrorException::NOT_BIND_BANKCARD);
            }

            $authUserModel->username = $params->newphone;
            $authUserModel->phone = $params->newphone;
            $authUserModel->display_name = mask_string($params->newphone, 3, 6);

            if ($authUserModel->save()) {
                return array(
                    'code' => 0,
                    'message' => '绑定手机修改成功'
                );
            }
            //
            throw new AllErrorException(AllErrorException::MODIFY_BIND_PHONE_FAIL);
        }
        //错误提示
        throw new AllErrorException(AllErrorException::VALID_SMS_FAIL);
    }

    /**
     * 修改绑定手机页面，获取短信、语音验证码
     *
     * @JsonRpcMethod
     *
     * @param $params
     *
     * @return mixed
     * @throws AllErrorException
     */
    public function secure_sms_code($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //检查新手机号格式
        $encMobile = Common::checkMobileAndEnc($params->mobile);
        if ($encMobile === false) {
            throw new AllErrorException(AllErrorException::VALID_PHONE_FAIL);
        }

        //验证手机号码是否已经被占用
        if (Common::checkMobileIsUsed($encMobile) !== false) {
            throw new AllErrorException(AllErrorException::VALID_PHONE_USED);
        }

        //发送短信 || 语音验证码
        $sms = isset($params->send_voice) && $params->send_voice == 1 ? false : true;

        //发送短信或语音
        $smsHandle = new Captcha($params->mobile, self::MODIFY_PHONE_SMS);
        $data = $smsHandle->send('change_bind_phone_code', $sms);
        $accessLog = $smsHandle->getAccessLog($sms);

        return array(
            'code' => 0,
            'message' => 'success',
            'data' => array(
                'key' => $data['key'],
                'call_num' => $accessLog['counter']
            )
        );
    }

    /**
     * 是否设置交易密码
     *
     * @JsonRpcMethod
     */
    public function isSetTradePwd()
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        $userModel = new \Model\AuthUser($this->userId);
        $tradePassword = $userModel->trade_pwd;
        $isset = !empty($tradePassword);

        return array(
            'code' => 0,
            'message' => 'success',
            'isset' => $isset
        );
    }
}