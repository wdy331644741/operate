<?php


namespace App\service\rpcserverimpl;

use Lib\Curl\Curl;
use App\service\exception\AllErrorException;

class WechatRpcImpl extends BaseRpcImpl
{
    const API_TOKEN_GET = 'https://api.weixin.qq.com/cgi-bin/token';

    const API_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';

    private $appId;

    private $secret;

    private $redis;

    private $curl;

    public function __construct()
    {
        $this->appId = config("WECHAT.appid");
        $this->secret = config("WECHAT.secret");
        $this->redis = getReidsInstance();
        $this->curl = new Curl();
    }

    /**
     * Get config json for jsapi.
     *
     * @JsonRpcMethod
     *
     * @return array
     * @throws AllErrorException
     */
    public function wechatShareConf()
    {
        $url = getenv('HTTP_REFERER');

        $signPackage = $this->signature($url);

        return array(
            'code' => 0,
            'message' => 'success',
            'sign_package' => $signPackage
        );
    }


    //获取acesstoken
    protected function getAccessToken()
    {
        $container = $this->redis->hget('wechat', $this->appId);

        if (empty($container['access']) ||
            (time() - $container['access']['create_time']) > 7000
        ) {
            $container = json_decode($container, true);

            $params = [
                'appid' => $this->appId,
                'secret' => $this->secret,
                'grant_type' => 'client_credential',
            ];
            $accessToken = $this->curl->get(self::API_TOKEN_GET, $params);
            if (isset($accessToken->errcode)) {
                throw new AllErrorException(
                    1000,
                    [],
                    'getAccessToken error: ' . $accessToken->errmsg
                );
            }
            $container['access']['token'] = $accessToken->access_token;
            $container['access']['create_time'] = time();

            $this->redis->hset('wechat', $this->appId, json_encode($container));
        }
        return $container['access']['token'];
    }

    /**
     * Build signature.
     *
     * @param string $url
     *
     * @return array
     */
    public function signature($url)
    {
        $nonce = str_random(10);
        $timestamp = time();
        $ticket = $this->ticket();
        $sign = [
            'appId' => $this->appId,
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'signature' => $this->getSignature($ticket, $nonce, $timestamp, $url),
        ];
        return $sign;
    }

    /**
     * Sign the params.
     *
     * @param string $ticket
     * @param string $nonce
     * @param int $timestamp
     * @param string $url
     *
     * @return string
     */
    public function getSignature($ticket, $nonce, $timestamp, $url)
    {
        return sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}");
    }

    /**
     * Get jsticket.
     *
     * @return mixed
     * @throws AllErrorException
     */
    public function ticket()
    {
        $container = $this->redis->hget('wechat', $this->appId);
        $container = json_decode($container, true);

        if (empty($container['ticket']) ||
            (time() - $container['ticket']['create_time']) > 7000
        ) {
            $params = [
                'access_token' => $this->getAccessToken(),
                'type' => 'jsapi',
            ];

            $ticket = $this->curl->get(self::API_TICKET, $params);

            if (empty($ticket->ticket)) {
                throw new AllErrorException(
                    1000,
                    [],
                    'get ticket error：' . $ticket->errmsg
                );
            }
            $container['ticket']['ticket'] = $ticket->ticket;
            $container['ticket']['create_time'] = time();
            $this->redis->hset('wechat', $this->appId, json_encode($container));
        }

        return $container['ticket']['ticket'];
    }
}
