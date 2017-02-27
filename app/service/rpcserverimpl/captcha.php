<?php
/**
 * Author     : newiep
 * CreateTime : 2016-07-20 12:16
 * Description: 短信验证码发送
 */

namespace App\service\rpcserverimpl;

use Lib\Session;
use App\service\exception\AllErrorException;
use Model\AuthCaptchaRatethrottle;
use Model\AuthIpRatethrottle;

class Captcha
{
    /**
     * 验证码操作标识
     */
    const CAPTCHA_HANDLE = "captcha";

    /**
     * @var 手机号
     */
    protected $mobile;

    /**
     * @var 接口唯一标识
     */
    protected $limit;

    /**
     * @var 验证码标识
     */
    protected $captchaFlag;

    /**
     * @var Session
     */
    protected $sessionHandle;

    /**
     * @var 访问记录
     */
    protected $accessLog;

    public function __construct($mobile, $captchaFlag, $limit = true)
    {
        $this->mobile = $mobile;
        $this->captchaFlag = $captchaFlag;
        $this->limit = $limit;
        $this->ratethrottle = new AuthCaptchaRatethrottle();

        $this->sessionHandle = new Session();
    }

    //获取验证码session标识
    public static function getSessionFlag($flag, $mobile)
    {
        return $flag . $mobile;
    }

    //验证码验证
    public static function valid($key, $value, $mobile, $flag)
    {
        //验证码配置
        $captchaConf = config("CAPTCHAT");

        //验证码记录模型
        $captchaModel = new AuthCaptchaRatethrottle();

        $sessionHandle = new Session();
        $sessionFlag = self::getSessionFlag($flag, $mobile);
        $codeData = $sessionHandle->get($sessionFlag);
        //验证码不存在
        if (empty($codeData)) {
            return false;
        }

        //获取未验证次数
        $notValidNum = $captchaModel->getNotValidNum($mobile, $codeData['sms']);

        //获取未验证码次数
        if ($notValidNum >= $captchaConf['frozen_max_num']) {
            throw new AllErrorException(AllErrorException::API_BUSY, [], '请联系客服进行验证');
        }

        //验证码失效检测
        if ((time() - $codeData['create_time']) > $captchaConf['captcha_expire']) {
            throw new AllErrorException(
                AllErrorException::VALID_SMS_FAIL, [], '短信验证码已过期，请重新获取'
            );
        }

        //验证码错误次数到达上限
        if ($codeData['error_num'] >= 5) {
            throw new AllErrorException(
                AllErrorException::VALID_SMS_FAIL, [], '验证码错误频繁，请稍后获取'
            );
        }

        //验证
        if (Common::valid($key, $value, $sessionFlag)) {
            //删除 session 验证码
            $sessionHandle->set($sessionFlag, array());

            //重置验证码发送记录
            $captchaModel->resetSmsLog($mobile, $codeData['sms']);
            return true;
        } else {
            //清零获取未验证次数
            $captchaModel->resetSmsLog($mobile, $codeData['sms'], ['not_valid' => 0]);

            // session 验证失败增加
            $codeData['error_num'] += 1;
            $sessionHandle->set($sessionFlag, $codeData);
            return false;
        }
    }

    //发送
    public function send($tmplIndex, $sms = true)
    {
        //检查接口调用限制
        if (!$this->limit || $this->checkedIpLimit() && $this->checkedAccessLimit($sms)) {

            //获取验证码数据
            $data = $this->getCaptchaData($this->captchaFlag, $this->mobile, $sms);

            //发送短信或语音
            $tmplIndex = $sms ? $tmplIndex : 'voice_code';
            //调动短信/语音接口，发送短信
            Common::sendMessage($this->mobile, $tmplIndex, $data);

            return $data;
        }
    }

    //获取当前接口访问记录
    public function getAccessLog($sms)
    {
//        //设置验证码记录生存周期
//        $this->ratethrottle->setCycleTime($cycle_time);
        $accessLog = $this->ratethrottle->initSmsLog($this->mobile, $sms);
        $accessLog['last_time'] = strtotime($accessLog['last_time']);
        $accessLog['create_time'] = strtotime($accessLog['create_time']);
        return $accessLog;
    }

    //ip限制检查
    public function checkedIpLimit()
    {
        $limitNum = config("CAPTCHAT.ip_limit_num"); //ip限制配置

        $ipThrottle = new AuthIpRatethrottle();

        //读取ip操作记录
        $ipLog = $ipThrottle->initIpLog(get_client_ip(), self::CAPTCHA_HANDLE);

        if ($ipLog['counter'] >= $limitNum) {
            throw new AllErrorException(AllErrorException::API_BUSY);
        }

        return $ipThrottle->increment($ipLog['id']);
    }

    //短信验证码接口限制
    private function checkedAccessLimit($sms)
    {
        //读取短信限制配置
        $captchaConf = config('CAPTCHAT');

        //调用记录
        $accessLog = $this->getAccessLog($sms);

        //获取未验证码次数
        if ($accessLog['not_valid'] >= $captchaConf['frozen_max_num']) {
            throw new AllErrorException(AllErrorException::API_BUSY, [], '请联系客服进行验证');
        }

        //根据规则过滤
        foreach ($captchaConf['send_filter'] as $filter) {
            if ($this->isLimited($accessLog, $filter['interval'], $filter['limit'])) {
                //距离下次可发送短信剩余时间
                $lastTime = $filter['interval'] + $accessLog['create_time'] - time();

                if ($lastTime < 60) {
                    $msg = "请{$lastTime}秒后重新获取";
                } elseif ($lastTime < 3600) {
                    $lastMinus = ceil(bcdiv($lastTime, 60, 1));
                    $msg = "请{$lastMinus}分钟后重新获取";
                } else {
                    $lastHours = ceil(bcdiv($lastTime, 3600, 1));
                    $msg = "请{$lastHours}小时后重新获取";
                }
                throw new AllErrorException(AllErrorException::API_BUSY, [], $msg);
            }
        }

        //更新验证码记录
        return $this->ratethrottle->increment($accessLog['id']);
    }

    //获取要发送的验证码数据
    protected function getCaptchaData($captchaFlag, $mobile, $sms = true)
    {
        //验证码未验证保留值有效期
        $expire = config("CAPTCHAT.captcha_expire");

        //验证码标识
        $captchaFlag = $captchaFlag . $mobile;

        $data = $this->sessionHandle->get($captchaFlag);

        //记录当前是短信 还是语音
        $data['sms'] = $sms;

        //未设置或已过期，重新生成
        if (!isset($data['create_time']) || (time() - $data['create_time']) > $expire) {

            $code = mt_rand(100000, 999999); //待发送短信验证码

            //session存储验证码
            $data['key'] = str_random();
            $data['code'] = $code;
            $data['mobile'] = $mobile;
            $data['error_num'] = 0;
            $data['create_time'] = time();
            $this->sessionHandle->set($captchaFlag, $data);

        } else {
            //重新获取，错误次数清空
            $data['error_num'] = 0;
            $this->sessionHandle->set($captchaFlag, $data);
        }

        return $data;
    }

    /**
     * 验证码间隔限制检查
     *
     * @param $smsLog
     * @param $interval
     * @param $maxNum
     *
     * @return bool
     */
    protected function isLimited($smsLog, $interval, $maxNum)
    {
        if (isset($smsLog['create_time']) && isset($smsLog['counter'])) {
            if (
                time() - $smsLog['create_time'] < $interval &&
                $smsLog['counter'] >= $maxNum
            ) {
                return true;
            }
        }
        return false;
    }
}