<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;

/**
 * 复投发放体验金
 * @pageroute
 */
function redeliveryExperience(){
	logs('记录复投发放体验金:' . PHP_EOL . var_export($_POST, true), 'redeliveryExperience');
}