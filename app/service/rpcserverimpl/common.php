<?php
/**
 * Author     : newiep
 * CreateTime : 19:26
 * Description: 公共接口服务
 */

namespace App\service\rpcserverimpl;

use Lib\McQueue;
use Lib\JsonRpcClient;
use Lib\Session;
use Lib\UserData;
use Lib\Curl\Curl;
use App\service\exception\AllErrorException;

class Common {

    /**
     *  模板参数正则
     */
    const PATTERN = '/\{\{(\w+?)\}\}/';

    /**
     * 云信高速触发接口
     */
    const SEND_MESSAGE_API = "http://h.1069106.com:1210/Services/MsgSend.asmx";

    /**
     * @var Session
     */
    protected $sessionHandle;

    public function __construct()
    {
        $this->sessionHandle = new Session();
    }

    /**
     * 验证
     *
     * @param $key
     * @param $value
     * @param $flag
     *
     * @return bool
     */
    public static function valid($key, $value, $flag, $reset = true)
    {
        $sessionHandle = new Session();
        $codeData = $sessionHandle->get($flag);

        if (isset($codeData['key']) && $codeData['key'] == $key && strtoupper($codeData['code']) == strtoupper($value)) {
            $reset && $sessionHandle->del($flag);

            return true;
        }
        //记录日志
        $debugMsg = "验证码验证：" . PHP_EOL;
        $debugMsg .= "请求参数：" . PHP_EOL . var_export(func_get_args(), true) . PHP_EOL;
        $debugMsg .= "正确参数：" . PHP_EOL . var_export($codeData, true);
        logs($debugMsg, 'captcha');

        return false;
    }

    /**
     * 消息广播
     *
     * @param $messageFlag
     * @param array $data
     *
     * @return void
     */
    public static function messageBroadcast($messageFlag, $data = array())
    {
        //广播消息 封装
        $mq_client = new McQueue();
        if (is_array($messageFlag)) {
            foreach ($messageFlag as $flag) {
                $putStatus = $mq_client->put($flag, $data);
                if (!$putStatus) {
                    $error = $mq_client->getErrMsg();
                    logs($error, 'mc_queue');
                }
            }
        } else {
            $putStatus = $mq_client->put($messageFlag, $data);
            if (!$putStatus) {
                $error = $mq_client->getErrMsg();
                logs($error, 'mc_queue');
            }
        }
    }

    /**
     * 检查登录状态
     *
     * @return bool|null|string
     */
    public static function checkLoginStatus()
    {
        $sessionHandle = new Session();
        $userData = $sessionHandle->get(USER_DATA_SKEY);
        if (empty($userData) || empty($userData['user_id'])) {
            return false;
        }

        return $userData['user_id'];
    }

    /**
     * 检查是否实名
     *
     * @param $user_id
     *
     * @return array|bool
     */
    public static function identifyChecked($user_id)
    {
        //获取身份信息
        $authIdentify = new \Model\AuthIdentify();

        return $authIdentify->identifyChecked($user_id);
    }

    /**
     * 检查是否绑卡
     *
     * @param $user_id
     *
     * @return bool
     */
    public static function bindCardChecked($user_id)
    {
        $bankCardModel = new \Model\AuthBankCard();

        return $bankCardModel->isBindCard($user_id);
    }

    /**
     * 检查手机号是否已经被注册
     *
     * @param $encMobile
     *
     * @return bool
     */
    public static function checkMobileIsUsed($encMobile)
    {
        $auth_user_model = new \Model\AuthUser();
        //检查是否已经被注册
        if ($userInfo = $auth_user_model->getUserInfoByName($encMobile)) {
            return $userInfo;
        }

        return false;
    }

    /**
     * 调用短信接口
     *
     * @param $mobile
     * @param $node
     * @param $data
     * @param $custom
     *
     * @return void
     */
    public static function sendMessage($mobile, $node, $data = array())
    {
        $params = array(
            'phone'     => $mobile,
            'node_name' => $node,
            'tplParam'  => $data
        );

        return self::localApiCall((object) $params, "smsMessage", 'PushRpcImpl');
    }

    /**
     * 即将过期体验金
     * 发送极光推送
     */

    public static function sendJpush($data, $node)
    {
        $params = new \stdClass();
        $params->user_id = $data;
        $params->node_name = $node;


        return self::jsonRpcApiCall($params, "sendJpush", config("RPC_API.msg"), false);
    }

    /**
     * 发送站内信
     *
     * @param $userid
     * @param $node
     * @param $data
     * @param $custom
     *
     * @return mixed
     */
    public static function sendWebMessage($userid, $node, $data = array(), $custom = array())
    {
        $params = new \stdClass();
        $params->user_id = $userid;
        $params->nodeName = $node;
        $params->tplParam = $data;
        $params->customTpl = $custom;

        return self::jsonRpcApiCall($params, "send", config("RPC_API.msg"), false);
    }

    //编译模板
    public static function compileTmpl($template, $data)
    {
        //获取所有可替换变量
        preg_match_all(self::PATTERN, $template, $matches);

        //模板中没有需要替代的参数
        if (count($matches[0]) === 0) {
            return $template;
        }
        //代替换数组
        $replace = array();

        //检查传递参数是否和被替换参数一一对应
        foreach ($matches[1] as $val) {
            //按顺序放入数组（下面替换顺序很重要）
            array_push($replace, $data[ $val ]);
        }

        //替换模板中的变量
        return str_replace($matches[0], $replace, $template);
    }

    /**
     * 其他服务rpc接口统一调用
     *
     * @param $data
     * @param $method
     * @param $url
     * @param array $config
     *$
     *
     * @return mixed
     */
    public static function jsonRpcApiCall(
        $data, $method, $url, $debug = true, $config = array('timeout' => 40)
    )
    {
        $rpcClient = new JsonRpcClient($url, $config);
        if (is_array($data)) {
            $result = call_user_func_array(array($rpcClient, $method), $data);
        } else {
            $result = call_user_func(array($rpcClient, $method), $data);
        }

        //记录日志
        if (config('DEBUG', false) || isset($result['error'])) {
            self::debugTrace($data, $method, $result);
        }

        //错误处理
        $debug && self::debugTrace($data, $method, $result) && self::jsonRpcErrorHandle($result);

        return $result;
    }

    //本地服务器rpc接口调用
    public static function localApiCall($data, $method, $rpc)
    {
        $rpcReflect = new \ReflectionClass(__NAMESPACE__ . '\\' . $rpc);
        $rpcInstance = $rpcReflect->newInstanceArgs();

        if (is_array($data)) {
            return call_user_func_array(array($rpcInstance, $method), $data);
        }

        return call_user_func(array($rpcInstance, $method), $data);
    }

    /**
     * 统一接口结果处理
     *
     * @param $result
     *
     * @throws AllErrorException
     * @throws \Exception
     */
    public static function jsonRpcErrorHandle($result)
    {
        if (isset($result['error'])) {

            //业务方面错误
            if ($result['error']['code'] > 0) {
                $data = isset($result['error']['data']) ? $result['error']['data'] : array();
                throw new AllErrorException(
                    $result['error']['code'], $data, $result['error']['message']
                );

            } else {
                throw new AllErrorException(AllErrorException::SERVER_ERROR);
            }
        }
    }

    //记录日志
    public static function debugTrace($data, $method, $result)
    {
        //记录日志
        $debugMsg = "接口{$method}请求结果：" . PHP_EOL;
        $debugMsg .= "请求参数：" . PHP_EOL . var_export($data, true) . PHP_EOL;
        $debugMsg .= "响应结果：" . PHP_EOL . var_export($result, true);
        logs($debugMsg, $method);

        return true;
    }

    /**
     * 获取用户session 数据
     *
     * @param $name
     * @return bool|null|string
     */
    public static function getUserData($name)
    {
        return UserData::get($name);
    }

}

