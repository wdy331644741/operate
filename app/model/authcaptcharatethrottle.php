<?php
namespace Model;
class AuthCaptchaRatethrottle extends Model
{
    //验证码记录最长有效期
    private $cycleTime;

    public function __construct($pkVal = '', $cycleTime = 86400)
    {
        parent::__construct('auth_captcha_ratethrottle');
        if ($pkVal)
            $this->initArData($pkVal);

        $this->cycleTime = $cycleTime;
    }

    //设置验证码记录有效期
    public function setCycleTime($maxtime)
    {
        $this->cycleTime = $maxtime;
        return true;
    }

    //初始化并获取数据记录
    public function initSmsLog($mobile, $isSms)
    {
        $smsLog = $this->where("`mobile` = '{$mobile}' and `is_sms` = '{$isSms}'")->get()->rowArr();

        if (!empty($smsLog)) {
            $interval = time() - strtotime($smsLog['create_time']);
            if ($interval > $this->cycleTime) {
                $this->update(
                    array(
                        'counter' => 0,
                        'not_valid' => 0,
                        'last_time' => date('Y-m-d H:i:s'),
                        'create_time' => date('Y-m-d H:i:s')
                    ),
                    array('id' => $smsLog['id'])
                );
            }
        } else {
            $smsLog['id'] = $this->add(array(
                'mobile' => $mobile,
                'is_sms' => $isSms,
                'last_time' => date('Y-m-d H:i:s'),
                'create_time' => date('Y-m-d H:i:s')
            ));
        }

        return $this->where(array('id' => $smsLog['id']))->get()->rowArr();
    }

    //获取验证码未验证码次数
    public function getNotValidNum($mobile, $isSms)
    {
        $log = $this->fields('not_valid')
            ->where("`mobile` = '{$mobile}' and `is_sms` = '{$isSms}'")
            ->get()->rowArr();

        return empty($log) ? 0 : $log['not_valid'];
    }

    //自增发送和未验证次数
    public function increment($id)
    {
        $data = array(
            'counter' => 'counter + 1',
            'not_valid' => 'not_valid + 1',
            'last_time' => date('Y-m-d H:i:s')
        );
        return $this->where(array('id' => $id))->upd($data);
    }

    //清空验证码发送记录
    public function resetSmsLog($mobile, $isSms, $data = [])
    {
        if (empty($data)) {
            $data = array(
                'counter' => 0,
                'not_valid' => 0,
                'last_time' => date('Y-m-d H:i:s', 0),
                'create_time' => date('Y-m-d H:i:s', 0)
            );
        }

        return $this->where("`mobile` = '{$mobile}' and `is_sms` = '{$isSms}'")->upd($data);
    }
}