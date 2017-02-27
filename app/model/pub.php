<?php
namespace Model;
class Pub extends Model
{
    const STATUS_ENABLE=1;
    const STATUS_DISABLE=0;
    const LEVEL_A=1;
    const LEVEL_B=2;
    const LEVEL_C=3;
    const DEGREE_FIRSET=1;
    const DEGREE_SECOND=2;
    const DEGREE_THIRD=3;
    const DEGREE_FORTH=4;
    public static $STATUS_MAP = array(
        self::STATUS_ENABLE=>'启用', //启用
        self::STATUS_DISABLE=>'禁用', //禁用
    );
    public static $LEVEL_MAP=array(
        self::LEVEL_A=>'A',
        self::LEVEL_B=>'B',
        self::LEVEL_C=>'C',
        );
    public static $DEGREE_MAP=array(
        self::DEGREE_FIRSET=>'--',
        self::DEGREE_SECOND=>'-',
        self::DEGREE_THIRD=>'+',
        self::DEGREE_FORTH=>'+',
        );
    public function __construct($pkVal = '')
    {

    }
}
