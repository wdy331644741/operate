<?php
/**
 * Author     : newiep
 * CreateTime : 19:26
 * Description: 注册相关Rpc服务
 */

namespace App\service\rpcserverimpl;

use Lib\Curl\Curl;
use \Lib\Ip\Ip;
use App\service\Traits\Validator;
use App\service\Traits\Signature;
use App\service\exception\AllErrorException;

class AuthorizeRpcImpl extends BaseRpcImpl {

    use Validator, Signature;

    /**
     * 登录接口标识
     */
    const LOGIN_FLAG = 'login';

    /**
     * 注册短信、语音验证码session标识
     */
    const REGISTER_SMS_FLAG = 'session_reg_sms';

    /**
     * 登录注册接口
     *
     * @JsonRpcMethod
     */
    public function signin($params)
    {
        if (empty($params->sms_key)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '请先获取短信验证码');
        }

        if (empty($params->sms_code)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '请填写短信验证码');
        }

        //接口必要参数
        if (empty($params->username)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '请填写手机号');
        }

        //手机号+验证码
        $captchaValid = Captcha::valid($params->sms_key, $params->sms_code, $params->username, self::REGISTER_SMS_FLAG);

        if (config("DEBUG_SIGNIN", false) || $captchaValid) {

            $authUser = new \Model\AuthUser();
            //解析UA头
            $userAgentInfo = getUAInfo();

            //ip 信息
            $clientIp = get_client_ip();
            $ipArea = Ip::find2Area($clientIp);

            //渠道
            $channel = !empty($params->channel) ? $params->channel : '';

            //邀请码
            $inviteCode = !empty($params->invite_code) ? $params->invite_code : '';

            $userInfo = $authUser->getUserInfoByName($params->username);
            //账户是否冻结
            if ($userInfo && $userInfo->is_active == 0) {
                throw new AllErrorException(AllErrorException::VALID_CAPTCHA_FAIL, [], '账号已冻结');
            }

            if (empty($userInfo)) {
                $data['username'] = $params->username;
                $data['phone'] = $params->username;
                $data['password'] = '';
                $data['display_name'] = mask_string($params->username, 3, 6);
                $data['from_platform'] = $userAgentInfo['platform'];
                $data['system'] = $userAgentInfo['system'];
                $data['from_channel'] = $channel;
                $data['create_time'] = date('Y-m-d H:i:s');//注册时间
                $data['last_ip'] = $clientIp;
                $data['ip_area'] = $ipArea ? $ipArea : '';
                $data['last_login'] = date('Y-m-d H:i:s');

                //邀请来源和自己的邀请码
                if (!empty($inviteCode)) {
                    $data['from_user_id'] = $authUser->getUidByInviteCode($inviteCode);
                }
                //保存用户注册数据
                $userInfo = $this->saveUserData($data);
            } else {
                //用户基本信息
                $userInfo = $authUser->getUserBasicInfo($userInfo->id);

                //存储用户信息数据
                $this->saveLoginSession($userInfo);

                $authUser->initArData($userInfo->id);
                //更新登录信息
                $authUser->last_login = date('Y-m-d H:i:s');
                $authUser->system = $userAgentInfo['system'];
                $authUser->last_ip = $clientIp;
                $authUser->ip_area = $ipArea ? $ipArea : '';
                $authUser->save();
            }

            //记录session_id
            $this->recordSid($userInfo->id);

            //清除记录限制
            $this->resetRatethrottle($userInfo->id, self::LOGIN_FLAG, true);

            //注册成功
            return array(
                'code'    => 0,
                'message' => '登录成功',
                'data'    => $userInfo
            );
        }
        //错误提示
        throw new AllErrorException(AllErrorException::VALID_SMS_FAIL);

    }

    /**
     * 登录状态接口
     *
     * @JsonRpcMethod
     *
     * @return array
     */
    public function loginStatus()
    {
        //检查登录状态
        $status = ($this->checkLoginStatus() === false) ? 0 : 1;

        return array(
            'code'    => 0,
            'message' => 'success',
            'status'  => $status
        );
    }

    /**
     * 登出
     *
     * @JsonRpcMethod
     */
    public function signout()
    {
        $userInfo = $this->sessionHandle->get(USER_DATA_SKEY);
        if (!empty($userInfo) && $userInfo['user_id']) {
            $this->sessionHandle->set(USER_DATA_SKEY, null);
            invalidUserProfileCache($userInfo['user_id']);
        }

        return array(
            'code'    => 0,
            'message' => 'success'
        );
    }

    /**
     * 用户名是否被注册
     *
     * @JsonRpcMethod
     */
    public function registered($params)
    {
        //接口必要参数
        if (!isset($params->username)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '请填写用户名');
        }

        $isRegisted = 0;
        $invite_code = '';

        //验证用户名（手机号）
        if (!$this->validatePhone($params->username)) {
            throw new AllErrorException(AllErrorException::VALID_PHONE_FAIL);
        }

        //检查是否已经被注册
        $userInfo = Common::checkMobileIsUsed($params->username);
        if ($userInfo !== false) {
            $isRegisted = 1;
            $invite_code = $userInfo->invite_code;
        }

        return array(
            'code'        => 0,
            'message'     => 'success',
            'is_registed' => $isRegisted,
            'invite_code' => $invite_code
        );
    }

    /**
     * 获取短信/语音验证码
     *
     * @JsonRpcMethod
     *
     * @param $params
     *
     * @return mixed
     * @throws \Exception
     */
    public function messageAuth($params)
    {
        //接口必要参数
        if (!isset($params->mobile)) {
            throw new AllErrorException(
                AllErrorException::API_MIS_PARAMS, [], '请填写手机号码'
            );
        }

        if (!$this->validatePhone($params->mobile)) {
            throw new AllErrorException(AllErrorException::VALID_PHONE_FAIL);
        }

        //发送短信 || 语音验证码
        $sms = !isset($params->send_voice) || $params->send_voice != 1;

        //发送短信或语音
        $smsHandle = new Captcha($params->mobile, self::REGISTER_SMS_FLAG);
        $data = $smsHandle->send('signin', $sms);
        $accessLog = $smsHandle->getAccessLog($sms);

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => array(
                'key'      => $data['key'],
                'call_num' => $accessLog['counter']
            )
        );
    }

    //新增注册用户数据及成功处理
    protected function saveUserData($data)
    {
        $auth_user_model = new \Model\AuthUser();

        //插入
        $newId = $auth_user_model->add($data);
        if ($newId == false) {
            logs($data, 'mysql_save_error');
            throw new AllErrorException(AllErrorException::SAVE_USER_FAIL);
        }

        //生成邀请码
        $auth_user_model->update(array('invite_code' => generate_invite_code($newId)), array('id' => $newId));

        //用户基本信息
        $user_info = $auth_user_model->getUserBasicInfo($newId);

        //初始化资产数据
        (new \Model\MarginMargin())->initMarginData($newId);
        $params = array(
            'user_id'     => $newId,
            'user_name'   => $user_info->username,
            'user_mobile' => $user_info->phone
        );
        $response = Common::jsonRpcApiCall((object) $params, 'registerUser', config('RPC_API.projects'));
        if (!isset($response['result']['code']) || $response['result']['code'] != 0) {
            logs($response, 'registerUser');
        }
        //存储用户信息数据(登录)
        $this->saveLoginSession($user_info);

        //记录session_id
        $this->recordSid($user_info->id);

//        //广播消息
//        Common::messageBroadcast('addExperience', array(
//            'user_id'   => $newId,
//            'node_name' => 'register',
//            'time'      => date("Y-m-d H:i:s")
//        ));

        //代替消息通知的方式，改为触发后返回
        $triggerUrl = config("BASE_HOST") . "/mcqueue.php?c=activities&a=trigger";
        $curl = new Curl();
        $curl->post($triggerUrl, array(
            'user_id'   => $newId,
            'node_name' => 'register',
            'time'      => date("Y-m-d H:i:s")
        ));
        return $user_info;
    }

}