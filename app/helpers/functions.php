<?php
if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     *
     * @return void
     */
    function dd()
    {
        array_map(function ($x) {
            var_dump($x);
        }, func_get_args());
        die();
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip()
    {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        foreach ($keys as $key) {
            if (array_key_exists($key, $_SERVER)) {
                foreach (explode(',', $_SERVER[ $key ]) as $ip) {
                    $ip = trim($ip); // just to be safe

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return 'unknow';
    }
}

if (!function_exists('str_random')) {
    /**
     * 生成一组随机字符串
     *
     * @param int $length
     *
     * @return string
     */
    function str_random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = openssl_random_pseudo_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}

if (!function_exists('mask_string')) {
    /**
     * 字符串打码，手机号、身份证
     *
     * @param $input
     * @param $start
     * @param $length
     * @param string $symbol
     *
     * @return mixed
     */
    function mask_string($input, $start, $length, $symbol = '*')
    {
        $sublen = strlen(substr($input, $start, $length));

        return substr_replace($input, str_pad('', $sublen, $symbol), $start, $length);
    }
}

if (!function_exists('generate_orderid')) {
    /**
     * 生成商户唯一订单id
     *
     * @return string
     */
    function generate_orderid($prefix = '')
    {
        $tmp = str_replace('.', '', microtime(true)); //毫秒

        return $prefix . str_pad($tmp, 15, '0') . mt_rand(1000, 9999);
    }
}


if (!function_exists('config')) {
    /**
     * 读取配置,支持多维，.分割
     *
     * @param  string $key
     * @param  mixed $default
     *
     * @return mixed
     */
    function config($key, $default = null)
    {
        $array = $GLOBALS['config'];

        if (!is_array($array)) {
            return $default;
        }

        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[ $key ];
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[ $segment ];
            } else {
                return $default;
            }
        }

        return $array;
    }
}

if (!function_exists('generate_invite_code')) {
    /**
     * 邀请码生成函数
     *
     * @param $userId
     *
     * @return string
     */
    function generate_invite_code($userId)
    {
        $codeSet = "wxyz";//补位字符集

        $hex32 = base_convert($userId, 10, 32);//id转32位
        $fillStr = "";

        //如果32进制小于6位
        if (($len = strlen($hex32)) < 6) {
            while (6 - $len) {
                $fillStr .= $codeSet[ mt_rand(0, 3) ];
                $len++;
            }
        }

        return $fillStr . $hex32;
    }
}

if (!function_exists('array_orderby')) {
    //多维数组排序
    function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[ $key ] = $row[ $field ];
                $args[ $n ] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);

        return array_pop($args);
    }
}

if (!function_exists('parting_table_name')) {

    /**
     * 根据用户id映射分表表名
     *
     * @param $userId
     * @param $table
     *
     * @return string
     */
    function parting_table_name($userId, $table)
    {
        $num = 36;//分表的数量
        $hash = sprintf("%u", crc32($userId));
        $mod = intval(fmod($hash, $num));
        $tableName = $table . ($mod + 1);

        return $tableName;
    }
}

if (!function_exists('generateToken')) {
    /**
     * 生成验签 token
     * @param $data
     * @param $secret
     * @return string
     */
    function generateToken($data, $secret)
    {
        ksort($data);

        return hash('sha256', http_build_query($data) . $secret);
    }
}

if (!function_exists('getUAInfo')) {

    /**
     * 分析请求头
     *
     * @param null $item
     *
     * @return array|mixed
     */
    function getUAInfo($item = null)
    {
        $header_arr = getallheaders();
        $agent = isset($header_arr['User-Agent']) ? $header_arr['User-Agent'] : '';

        $parser = new \Lib\ParseUA();
        $result = $parser->parse($agent)->getParseInfo();

        if (!empty($item) && isset($result[$item])) {
            return $result[$item];
        }
        return $result;
    }
}

if (!function_exists('encrypt_aes')) {
    /**
     * 生成会话token
     *
     * @param $str
     * @param $key
     * @param $iv
     * @return string
     */
    function encrypt_aes($str, $key, $iv)
    {
        return \Lib\Auth::encrypt_aes($str, $key, $iv);
    }
}

if (!function_exists('decrypt_aes')) {
    /**
     * 反解会话token
     *
     * @param $str
     * @param $key
     * @param $iv
     * @return string
     */
    function decrypt_aes($str, $key, $iv)
    {
        return \Lib\Auth::decrypt_aes($str, $key, $iv);
    }
}


function node_merges($node, $access = array(), $pid = 0, $id = 'id')
{
    $arr = array();
    foreach ($node as $k => $nodeList) {
        if (is_array($node)) {
            $nodeList['access'] = in_array($nodeList[ $id ], $access) ? 1 : 0;
        }
        if ($nodeList['parent_id'] == $pid) {
            $nodeList['child'] = node_merges($node, $access, $nodeList[ $id ]);
            if (empty($nodeList['child'])) {
                $nodeList['show'] = 0;
            } else {
                $nodeList['show'] = 1;
            }
            $arr[] = $nodeList;
        }
    }
    return $arr;
}

/**
 * 根据总数目，当前页数，返回分页后的Obj
 *
 * @param $countNum 查询的总数目
 * @param string $page 当前页数
 */
function getPageObj($countNum, $page = '1', $pageSize = '')
{
    $config = array(
        'total'        => $countNum,
        'pagesize'     => $pageSize ? $pageSize : C('PAGE_SIZE'),
        'current_page' => $page,
    );

    return $pagination = new \Lib\Pagination($config);
}


if (!function_exists('throwErrJosn')) {
    function throwErrJosn($errCode, $errMsg)
    {
        ajaxReturn(['err_code' => $errCode, 'err_msg' => $errMsg]);
    }
}

/**
 * 获取上传图片的url
 *
 * @return uploadUrl
 */
function getBaseUploadUrl()
{
    $storageObj = new \Storage\Storage();
    $uploadMsg = $storageObj->getUploadUrl('commonThumb');
    if ($uploadMsg['status'] == '200') {
        return $uploadMsg['msg'];
    }

    return false;
}

/**
 * 用户信息缓存失效
 *
 * @param $userid
 *
 * @return mixed
 * @throws Exception
 */
function invalidUserProfileCache($userid)
{
    $cacheKey = 'userinfo' . $userid;
    $redisInstance = getReidsInstance();

    return $redisInstance->expire($cacheKey, 0);
}

//二维数组去重
function assoc_unique($arr, $key)
{
    $tmpKey = array();
    $data = array();
    foreach ($arr as $item) {
        if (in_array($item[ $key ], $tmpKey)) {
            continue;
        }
        $tmpKey[] = $item[ $key ];
        $data[] = $item;
    }

    return $data;
}

/**
 * 信息修改为*号
 * @param $data
 * return $data
 */
function maskData($data)
{
    $sessionObj = new \Lib\Session();
    $session = $sessionObj->get('userData.admin_user');
    if ($session) {
        $adminUserRole = $session['user_role'];
        foreach ($data as $key => &$val) {
            if (is_array($val)) {
                if ($adminUserRole['id_number'] == 2) {
                    if (isset($val['id_number'])) {
                        $val['id_number'] = mask_string($val['id_number'], 6, 8);
                    }
                }
                if ($adminUserRole['phone'] == 2) {
                    if (isset($val['phone'])) {
                        $val['phone'] = mask_string($val['phone'], 3, 6);
                    }
                }
            } else {
                if ($adminUserRole['id_number'] == 2) {
                    if ($key == 'id_number') {
                        $data[ $key ] = mask_string($val, 6, 8);
                    }
                }
                if ($adminUserRole['phone'] == 2) {
                    if ($key == 'phone') {
                        $data[ $key ] = mask_string($val, 3, 6);
                    }
                }
            }

        }
    }

    return $data;
}

