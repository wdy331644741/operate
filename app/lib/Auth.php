<?php
/**
 * 加密类
 */

namespace Lib;

use InvalidArgumentException;

class Auth {

    /**
     * aes加密,PS:key如果不足16位,将以\0字符串补足
     * @param $data |string 需要加密的数据
     * @param $key |string key,必须为16位
     * @param $iv |string  iv,必须为16位
     * @return string
     */
    public static function encrypt_aes($data, $key, $iv = '')
    {
        if (empty($key)) {
            throw new InvalidArgumentException("加密的key不能为空");
        }
        if (empty($iv) || strlen($iv) != 16) {
            throw new InvalidArgumentException("向量iv无效");
        }
        $key = str_pad($key, 16, "\0");   // key必须为16位
        $encrypted = bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv));

        return rtrim($encrypted);
    }

    /**
     * aes解密,PS:key如果不足16位,将以\0字符串补足
     * @param $data |string 需要解密的数据
     * @param $key |string  key,必须为16位
     * @param $iv |string   iv,必须为16位
     * @return string
     */
    public static function decrypt_aes($data, $key, $iv = '')
    {
        if (empty($key)) {
            throw new InvalidArgumentException("加密的key不能为空");
        }
        if (empty($iv) || strlen($iv) != 16) {
            throw new InvalidArgumentException("向量iv无效");
        }
        $key = str_pad($key, 16, "\0");   // key必须为16位
        $data = hex2bin($data);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);

        return rtrim($decrypted);
    }

    /**
     * hash256加密,不可逆
     * @param $data
     * @return string
     */
    public static function sh256($data)
    {
        return hash('sha256', $data);
    }
}