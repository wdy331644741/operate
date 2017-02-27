<?php
namespace Lib;

class UserData {

    private $group;

    protected static $instance;

    public function __construct()
    {
        if (!$this->group) {
            $userHost = C('SERVER_PASSPORT');
            $host = parse_url($userHost, PHP_URL_HOST);
            if (!$host) {
                die('未配置 SERVER_PASSPORT');
            }
            $this->group = $host;
        }
    }

    static public function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    static public function get($name)
    {
        $instance = self::getInstance();
        return $instance->{$name};
    }

    static public function set($name, $value)
    {
        $instance = self::getInstance();

        //失效用户缓存
        invalidUserProfileCache($instance->get('user_id'));

        return $instance->{$name} = $value;
    }

    public function __get($name)
    {
        $sessionTool = new Session();
        $sessionTool->setGroup($this->group);
        $data = $sessionTool->get('userData.' . $name);
        $sessionTool->setGroup('');

        return $data;
    }

    public function __set($name, $value = '')
    {
        $sessionTool = new Session();
        $sessionTool->setGroup($this->group);
        $data = $sessionTool->set('userData.' . $name, $value);
        $sessionTool->setGroup('');

        return $data;
    }
}