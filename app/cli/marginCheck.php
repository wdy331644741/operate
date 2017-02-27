<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * 校验用户流水和余额是否相符
 * @pageroute
 */
function run()
{
    $result = [];
    $model = new \Model\MarginRecord();
    $authUser = new \Model\AuthUser();
    $userIds = $authUser->fields('id')->where("is_active != 0")->get()->resultArr();
    foreach ($userIds as $user) {
        $status = $model->checkMarginByRecord($user['id']);
        if(!$status) {
            $result[] = $user['id'];
        }
    }

    logs(var_export($result, true), 'marginCheck');
    echo 'complete';
}