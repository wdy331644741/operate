<?php
namespace Lib;

class MqClient
{
    private $ip;
    private $port;
    private $password;

    public function __construct($ip = "", $port = "", $password = "")
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->password = $password;
    }


    public function send($tag, $value)
    {
        $redis = getReidsInstance($this->ip, $this->port, $this->password);
        $json = json_encode(["tag" => $tag, "value" => $value]);
        $redis->LPUSH('msg_queue', $json);
    }

}
