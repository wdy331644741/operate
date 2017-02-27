<?php
/**
 * Author     : newiep
 * CreateTime : 2016-06-27 17:31
 * Description: 用户相关接口基类
 */

namespace App\service\rpcserverimpl;

use Lib\Session;
use Lib\JsonRpcService;
use Model\AuthIpRatethrottle;
use Model\AuthAccountRatethrottle;
use App\service\rpcserverimpl\ToolsRpcImpl as Tools;
use App\service\exception\AllErrorException;

class BaseRpcImpl extends JsonRpcService {

    //支付是否发送验证码
    const SMS_CONFIRM = 1;
    const NOT_SMS_CONFIRM = 0;

    //是否普通支付
    const NORMAL_RECHARGE = 1;
    const NOT_NORMAL_RECHARGE = 0;

    protected $userId;

    /**
     * @var Session
     */
    protected $sessionHandle;

    public function __construct()
    {
        $this->sessionHandle = new Session();
    }

    /**
     * 检查登录状态
     *
     * @return mixed
     * @throws AllErrorException
     */
    protected function checkLoginStatus()
    {
        $userData = $this->sessionHandle->get(USER_DATA_SKEY);

        //记录user_agent 信息
        $this->logUserAgent('user_agent');

        if (empty($userData) || empty($userData['user_id'])) {
            return false;
        }
        //设备检查
        config("MULTIPLE_SIGNIN") && $this->deviceProtect($userData['user_id']);

        return $userData['user_id'];
    }

    //检查接口ip限制
    protected function IpRatethrottle($handle, $maxNum, $interval = 60)
    {
        $ipThrottle = new AuthIpRatethrottle('', $interval);

        //读取ip操作记录
        $ipLog = $ipThrottle->initIpLog(get_client_ip(), $handle);

        if ($ipLog['counter'] >= $maxNum) {
            return false;
        }

        return $ipThrottle->increment($ipLog['id']);
    }

    //检查账户相关限制
    protected function incrementRatethrottle($userId, $handle)
    {
        if (empty($userId)) {
            return 0;
        }
        $accountThrottle = new AuthAccountRatethrottle();
        $accountThrottle->incrementFailedCounter($userId, $handle);

        //返回当前失败次数
        return $accountThrottle->currentFailedCounter($userId, $handle);
    }

    //清除限制记录数据
    protected function resetRatethrottle($userId, $handles, $resetIp = false)
    {
        //清除用户记录
        $accountThrottle = new AuthAccountRatethrottle();
        $accountThrottle->resetFailedCounter($userId, $handles);

        //是否同时清除ip
        if ($resetIp) {
            $ipThrottle = new AuthIpRatethrottle();
            $ipThrottle->reset($handles);
        }
    }

    /**
     * 验证图片或极验
     *
     * @param $params
     * @param bool $reset
     *
     * @return bool
     * @throws AllErrorException
     */
    protected function validCaptcha($params, $reset = true)
    {
        //验证图片验证码
        if (isset($params->img_key)) {
            if (!Common::valid($params->img_key, $params->img_code, Tools::IMG_CAPTCHA_FLAG, $reset)) {
                throw new AllErrorException(AllErrorException::VALID_CAPTCHA_FAIL);
            }
        }

        //验证极验验证码
        if (isset($params->challenge)) {
            //调用极验验证接口
            Common::localApiCall($params, 'geeCaptchaValid', 'ToolsRpcImpl');
        }

        return true;
    }

    /**
     * 冻结检查
     *
     * @param $userId 账户标识
     * @param $type self::FROZEN_TRADE_PWD | self::FROZEN_SIGNIN_PWD
     *
     * @return bool  true: 冻结 | false : 未冻结
     */
    protected function frozenChecked($userId, $type, $maxNum = null)
    {
        $frozenConf = config("FROZEN_CONFIG.{$type}");
        $cycleTime = empty($frozenConf['cycle_time']) ? PHP_INT_MAX : $frozenConf['cycle_time'];
        $maxNum = empty($maxNum) ? $frozenConf['trigger_num'] : $maxNum;

        $accountThrottle = new AuthAccountRatethrottle('', $cycleTime);
        $currentC = $accountThrottle->currentFailedCounter($userId, $type);
        if ($currentC >= $maxNum) {
            return true;
        }

        return false;
    }

    /**
     * 记录当前登录设备session id
     *
     * @param $userId
     */
    protected function recordSid($userId)
    {
        $redis = getReidsInstance();
        $device = getAgent('platform');

        if ($this->checkedDevice($device)) {
            $redis->hset(DEVICE_SID_CONTAINER, $userId, session_id());
        }
    }

    //设备保护，多设备登录
    protected function deviceProtect($userId)
    {
        $redis = getReidsInstance();
        $device = getAgent('platform');
        $currentSid = session_id();
        $oldSid = $redis->hget(DEVICE_SID_CONTAINER, $userId);

        if ($this->checkedDevice($device)) {
            //sessionId 不一致
            if (!empty($oldSid) && $currentSid != $oldSid) {
                $this->sessionHandle->set(USER_DATA_SKEY, array());
                throw new AllErrorException(AllErrorException::REMOTE_SIGNIN);
            }
        }
    }

    //检查是否在受限制列表
    protected function checkedDevice($device)
    {
        $device = strtoupper($device);
        $protectedDevice = empty(config('PROTECTED_DEVICES')) ? array('IOS', 'ANDROID') : config('PROTECTED_DEVICES');

        return in_array($device, $protectedDevice);
    }

    //记录请求user_agent状态
    protected function logUserAgent($filename)
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $cookie = isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : '';
        $msg = "host：" . PHP_EOL . $host . PHP_EOL;
        $msg .= "user_agent：" . PHP_EOL . $user_agent . PHP_EOL;
        $msg .= "cookie：" . PHP_EOL . $cookie . PHP_EOL;

        config('DEBUG') && logs($msg, $filename);
    }

    //获取操作标识
    protected function getHandleToken($handle)
    {
        $userData = $this->sessionHandle->get(USER_DATA_SKEY);
        //if exist
        $userData[ $handle ] = empty($userData[ $handle ]) ? str_random() : $userData[ $handle ];
        //save
        $this->sessionHandle->set(USER_DATA_SKEY, $userData);

        return array(
            'handle' => $handle,
            'token'  => $userData[ $handle ]
        );
    }

    //检查操作标识
    protected function validHandleToken($handle, $token, $reset = true)
    {
        $userData = $this->sessionHandle->get(USER_DATA_SKEY);

        if (empty($userData[ $handle ])) {
            return false;
        }

        //保存session中的token
        $c_token = $userData[ $handle ];

        if ($reset) {
            unset($userData[ $handle ]);
            $this->sessionHandle->set(USER_DATA_SKEY, $userData);
        }

        return $c_token == $token;
    }

    /**
     * 登陆成功后保存用户信息到session
     */
    public function saveLoginSession($userInfo)
    {
        $this->sessionHandle->set(USER_DATA_SKEY, array(
            'user_id'       => $userInfo->id,
            'user_name'     => $userInfo->username,
            'phone'         => $userInfo->phone,
            'display_name'  => $userInfo->display_name,
            'from_user_id'  => $userInfo->from_user_id,
            'register_time' => $userInfo->create_time,
            'last_login'    => $userInfo->last_login,
            'is_active'     => $userInfo->is_active,
            'withdraw_num'  => $userInfo->withdraw_num,
            'is_identify'   => $userInfo->is_identify,
            'is_bindcard'   => $userInfo->is_bindcard,
            'isset_tradepwd'=> $userInfo->isset_tradepwd
        ));
    }

    /**
     * 检查 IP 白名单
     */
    protected function checkedIpList()
    {
        //IP 白名单
        $whiteIpList = explode(';', config("WHITE_IP_LIST"));

        //请求端IP
        $ip = get_client_ip();

        if (!empty($whiteIpList) && in_array($ip, $whiteIpList)) {
            return true;
        }
        $msg = "white ip lists:" . PHP_EOL;
        $msg .= var_export($whiteIpList, true) . PHP_EOL;
        $msg .= "current client ip:" . PHP_EOL;
        $msg .= var_export($ip, true);
        logs($msg, 'white_ip_list');

        return false;
    }

    //调用银行卡查询接口
    protected function getCardInfo($userId, $cardno)
    {
        $params = new \stdClass();
        $params->identityid = $userId;
        $params->cardno = (string) $cardno;

        $response = Common::jsonRpcApiCall($params, 'bankCardCheck', config('RPC_API.pay'));

        if (!isset($response['result'])) {
            throw new AllErrorException(AllErrorException::SERVER_ERROR);
        }
        $result = $response['result'];

        //判断是否支持该银行
        if (isset($result['isvalid']) && $result['isvalid'] != 1) {
            throw new AllErrorException(AllErrorException::CARD_NOT_SUPPORT);
        }

        //银行渠道信息
        if (
            empty($result['bankcode']) ||
            empty($result['bankname']) ||
            empty($result['channel_code'])
        ) {
            throw new AllErrorException(
                AllErrorException::SERVER_ERROR, [], '获取银行信息出错，请检查银行卡信息是否正确'
            );
        }

        //银行限额信息
        if (
            empty($result['days_quota']) ||
            empty($result['first_quota']) ||
            empty($result['times_quota'])
        ) {
            throw new AllErrorException(
                AllErrorException::SERVER_ERROR, [], '获取银行限额信息出错'
            );
        }

        return $result;
    }
}