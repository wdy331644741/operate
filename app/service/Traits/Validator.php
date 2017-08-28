<?php
namespace App\service\Traits;

trait Validator {

    public function validateMoney(&$money)
    {
        $money = trim($money);
        return filter_var($money, FILTER_VALIDATE_FLOAT) && $money == round($money, 2);
    }

    public function validatePhone(&$mobile)
    {
        $mobile = trim($mobile);
        return preg_match('/^1[0-9]{10}$/', $mobile);
    }

    public function validateIDnumber(&$idNumber)
    {
        $idNumber = strtoupper(trim($idNumber));
        //身份证号格式
        return preg_match('/^[0-9]{17}(\d|X|x)$/', $idNumber);
    }

    public function validateTradePwd($password)
    {
        return preg_match('/^[0-9]{6}$/', $password);
    }

}