<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");

use \App\service\rpcserverimpl\Common;


/**
 * 阶梯加息
 * 留存7天 给0.5加息劵
 * 留存14天 给1加息劵
 * @pageroute
 */
function index(){
	$couponName = "ladder_basis_1";
	$awardInterestoupon = new \Model\AwardInterestcoupon();
	$couponId = $awardInterestoupon->getCouponIdByName($couponName);
	// var_export($couponId);
	$marketingInterestoupon = new \Model\MarketingInterestcoupon();
	//获取昨天所有预发放
	// $date = date("Y-m-d");
	$date = "2017-05-29";
	$allMarketingData = $marketingInterestoupon->getAllDataByDay($date,$couponId['id']);
	var_export($allMarketingData);
	//遍历循环 发息
}