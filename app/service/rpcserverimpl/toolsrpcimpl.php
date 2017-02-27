<?php
/**
 * Author     : newiep
 * CreateTime : 2016-06-20 13:46
 * Description: 一些通用的接口类（类似图片验证码、短信验证码）
 */

namespace App\Service\Rpcserverimpl;

use App\service\exception\AllErrorException;

class ToolsRpcImpl extends BaseRpcImpl
{
    /**
     * 图片验证码标识
     */
    const IMG_CAPTCHA_FLAG = 'session_img_captcha';


    /**
     * 生成图片验证码接口
     *
     * @JsonRpcMethod
     *
     * @return mixed
     */
    public function captcha()
    {
        $key = str_random(); //验证码键值
        $code = strtoupper(str_random(5)); //验证码
        $img_src = U("service.php?c=account&a=captcha&sid=".session_id()."&_t=".time());

        //session存储
        $this->sessionHandle->set(self::IMG_CAPTCHA_FLAG, array('key'=>$key, 'code'=>$code));

        return array(
            "code" => 0,
            "message" => 'success',
            "data" => array(
                'key' => $key,
                'img_src' => $img_src
            )
        );
    }

    /**
     * 图片验证码验证接口
     *
     * @JsonRpcMethod
     *
     * @param $params
     *
     * @return array
     * @throws AllErrorException
     */
    public function captchaValid($params)
    {
        //验证
        if (!Common::valid($params->key, $params->code, self::IMG_CAPTCHA_FLAG)) {
            throw new AllErrorException(AllErrorException::VALID_CAPTCHA_FAIL);
        }
        return array(
            'code' => 0,
            'message' => 'success'
        );
    }

    /**
     * 极验预处理
     *
     * @JsonRpcMethod
     */
    public function geeCaptcha()
    {
        $GtSdk = new \Lib\GeetestLib(C("GEETEST_KEY"), C("GEETEST_SEC"));
        $status = $GtSdk->pre_process();

        //存储
        $this->sessionHandle->set('gtserver', $status);

        $data = $GtSdk->get_response();

        return array(
            'code' => 0,
            'message' => 'success',
            'data' => array(
                'gt' => $data['gt'],
                'challenge' => $data['challenge']
            )
        );
    }

    /**
     * 极验预处理
     *
     * @JsonRpcMethod
     */
    public function geeCaptchaValid($params)
    {
        //接口必要参数
        if (!isset($params->challenge) || !isset($params->validate) || !isset($params->seccode)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        $GtSdk = new \Lib\GeetestLib(C("GEETEST_KEY"), C("GEETEST_SEC"));

        if ($this->sessionHandle->get('gtserver') == 1) {
            $result = $GtSdk->success_validate($params->challenge, $params->validate, $params->seccode);
        } else {
            $result = $GtSdk->fail_validate($params->challenge, $params->validate, $params->seccode);
        }

        return array(
            'code' => 0,
            'message' => 'success',
            'result' => $result
        );
    }

}