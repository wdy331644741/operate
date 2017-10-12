<?php
/**
 * Author     : daiqing.
 * CreateTime : 2017/09/20
 * Description: 微信分享接口服务
 */

namespace App\service\rpcserverimpl;

use App\service\exception\AllErrorException;
use Lib\UserData;
use Model\AuthUser;
use Model\Model;
use Model\ReportFootprint;

class ShareRpcImpl extends BaseRpcImpl
{
    const APPID = 'wxa709d17699390559';
    const APP_SECRET = '2e0ee912a31457b869625d9bab9cb598';
  /*  const APPID = 'wxfbb874845f47535c';//测试号
    const APP_SECRET = '434c4790cfa637c1a045126e6318be15';*/
    private $appId;
    private $appSecret;

    public function __construct() {
        $this->appId = self::APPID;
        $this->appSecret = self::APP_SECRET;
    }
    /**
     * 获取签名数据
     *
     * @JsonRpcMethod
     */
    public function getSignPackage($params) {
        $jsapiTicket = $this->getJsApiTicket();
        if(!$jsapiTicket){
            return array(
                'error'=>100,
                'msg'=>'获取api_ticket失败'
            );
        }
        // 注意 URL 一定要动态获取，不能 hardcode.
       // $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        //$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url = $params->url;
        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新,放入redis中
        $redis = getReidsInstance();
        //$redis->hDel('WX_JSAPI_TICKET','jsapi_ticket','expire_time');
       // $redis->hDel('WX_ACCESS_TOKEN','access_token','expire_time');
        $data = $redis->hGetAll('WX_JSAPI_TICKET');
        logs($data,'wxshare');
        if (!isset($data['jsapi_ticket']) ||empty($data['jsapi_ticket'])|| $data['expire_time'] < time()) {
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
           // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            logs($res,'wxshare');
            $ticket = $res->ticket;
            if ($ticket) {
                $redis->hMset('WX_JSAPI_TICKET',array('jsapi_ticket'=>$ticket,'expire_time'=>time() + 7000));
            }
        } else {
            $ticket = $data['jsapi_ticket'];
        }
        return $ticket;
    }

    private function getAccessToken() {
        // access_token 应该全局存储与更新，放入redis中
        $redis = getReidsInstance();
        $data = $redis->hGetAll('WX_ACCESS_TOKEN');
        logs($data,'wxshare');
        if (!isset($data['access_token']) || empty($data['access_token']) || $data['expire_time'] < time()) {
            // 如果是企业号用以下URL获取access_token
            //$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            logs($res,'wxshare');
            $access_token = $res->access_token;
            if ($access_token) {
                $redis->hMset('WX_ACCESS_TOKEN',array('access_token'=>$access_token,'expire_time'=>time() + 7000));
            }
        } else {
            $access_token = $data['access_token'];
        }
        return $access_token;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl,CURLOPT_CAINFO,__WEBROOT__.'/cacert.pem');
        /*curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);*/
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

}
