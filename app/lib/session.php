<?php
namespace Lib;
/**
 * Class session
 *
 * @package Lib
 * 获取$_SESSION
 * Lib\Session::get();
 * 设置$_SESSION
 * Lib\Session::set(null,['name' => 'liuqi' , 'mobile' => '18811176547' , 'address' => ['company' => '海航' , 'home' => '霍营']]);
 * 删除$_SESSION某一项,注意这里只会把置变为NULL,而不会undefined.  (获取 设置 同样可以这样指定键名)
 * Lib\Session::del('address.company');
 */
class Session
{
    //session 名称
    const SESSION_NAME = "WANGLIBAO_TOKEN";

    private $group; //session数据组名

    public function __construct($sid = '')
    {
        self::init($sid);
    }

    public static function init($sid = '')
    {
        @session_name(self::SESSION_NAME);
        @ini_set("session.cookie_domain", C('COOKIE_DOMAIN'));

        //判断请求来源
        $device = getUAInfo('platform');

//        if ($device == 'IOS' || $device == 'ANDROID') {
            @ini_set('session.gc_maxlifetime', config('APP_TOKEN_MAXLIFETIME', 604800));
//        }

        $sessionId = empty($sid) ? I('cookie.'.self::SESSION_NAME, '') : $sid;
        if ($sessionId)
            session_id($sessionId);
        @session_start();
        @session_commit();
    }


    public function get($key = '')
    {
        @session_start();
        $data = $this->_session('get', $key);
        @session_commit();
        return $data;
    }

    public function set($key = '', $val)
    {
        @session_start();
        $status = $this->_session('set', $key, $val);
        @session_commit();
        return $status;

    }

    public function del($key = '')
    {
        @session_start();
        $status = $this->_session('del', $key);
        @session_commit();
        return $status;
    }

    private function _session($action, $key = '', $val = '')
    {
        //获取session分组
        $group = $this->getGroup();

        $data = &$_SESSION[$group];
        if ($key) {
            $deepKeys = explode('.', $key);
            foreach ($deepKeys as $deepKey) {
                if (isset($data[$deepKey])) {
                    $data =  &$data[$deepKey];
                } else {
                    if ($action == 'set') {
                        $data[$deepKey] = null;
                        $data = &$data[$deepKey];
                    } else {
                        return null;
                    }
                }
            }
        }
        switch ($action) {
            case 'get' :
                return $data;
            case 'set':
                $data = $val;
                return true;
            case 'del':
                $data = null;
                return true;
        }

    }

    //设置分组
    public function setGroup($group)
    {
        $this->group = $group;
        return true;
    }

    //获取分组
    public function getGroup()
    {
        if (!$this->group) {
            $baseHost = C('BASE_HOST');
            $host = parse_url($baseHost, PHP_URL_HOST);
            if (!$host) {
                die('未配置 BASE_HOST');
            }
            $this->group = $host;
        }
        return $this->group;
    }
}