<?php
/**
 * Created by PhpStorm.
 * User: sf
 * Date: 2016/6/16
 * Time: 14:58
 */
namespace Lib;
class AdminError
{
    const USER_ACTIVE = 1;//正常
    const USER_FORBIDDEN = 0;//禁用
    const DATA_NOT_EXISTS = 100;//数据不存在
    const DATA_SUCCESS = 200;//成功
    const DATA_ERROR = 300;//失败

    protected static $errorArray = array
    (
        self::USER_ACTIVE => '正常',
        self::USER_FORBIDDEN => '禁止',
        self::DATA_NOT_EXISTS => '数据不存在',
        self::DATA_SUCCESS => '成功',
        self::DATA_ERROR => '失败',
    );

}