<?php
namespace Model;
class MarginType extends Model
{
    static $type = array (
        'recharge_request' =>
            array (
                'type' => 'recharge_request',
                'type_to_cn' => '买入已审核',
            ),
        'recharge_ing' =>
            array (
                'type' => 'recharge_ing',
                'type_to_cn' => '买入处理中',
            ),
        'recharge_success' =>
            array (
                'type' => 'recharge_success',
                'type_to_cn' => '买入成功',
            ),
        'recharge_fail' =>
            array (
                'type' => 'recharge_fail',
                'type_to_cn' => '买入失败',
            ),
        'withdraw_request' =>
            array (
                'type' => 'withdraw_request',
                'type_to_cn' => '转出已受理',
            ),
        'withdraw_ing' =>
            array (
                'type' => 'withdraw_ing',
                'type_to_cn' => '转出已审核',
            ),
        'audit_fail' =>
            array (
                'type' => 'audit_fail',
                'type_to_cn' => '审核失败',
            ),
        'withdraw_success' =>
            array (
                'type' => 'withdraw_success',
                'type_to_cn' => '转出成功',
            ),
        'withdraw_fail' =>
            array (
                'type' => 'withdraw_fail',
                'type_to_cn' => '转出失败',
            ),
        'refund_interest' =>
            array (
                'type' => 'refund_interest',
                'type_to_cn' => '利息入账',
                'displayname' => '利息收益',
            ),
        'refund_increase' =>
            array (
                'type' => 'refund_increase',
                'type_to_cn' => '加息入账',
                'displayname' => '加息收益',
            ),
        'refund_exp_interest' =>
            array (
                'type' => 'refund_exp_interest',
                'type_to_cn' => '体验金利息入账',
                'displayname' => '体验金收益',
            ),
    );

    public function __construct($pkVal = '')
    {
        parent::__construct('margin_type');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    static public function getByActionName($name)
    {
        return isset(self::$type[$name]) ? self::$type[$name] : false;
    }

    static public function getTypeByActionName($name)
    {
        return self::getByActionName($name)['type'];
    }

    static public function getTypeToCnByActionName($name)
    {
        return self::getByActionName($name)['type_to_cn'];
    }

}