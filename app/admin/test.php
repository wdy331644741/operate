<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 17:36
 */
defined("__FRAMEWORKNAME__") or die("No permission to access!");

/**
 * 测试
 * @pageroute
 */
function index()
{
    $randData = array(
        ['id' => 1, 'probability' => 0.998],
        ['id' => 2, 'probability' => 0.002],
    );

    $rid = getRandChance($randData); //根据概率获取奖项id
    echo json_encode(['id'=>$rid], JSON_UNESCAPED_UNICODE);

    die();
}
