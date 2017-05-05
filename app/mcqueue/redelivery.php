<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;

/**
 * 复投发放体验金
 * @pageroute
 */
function redeliveryExperience(){

	logs('记录复投发放体验金:' . PHP_EOL . var_export($_POST, true), 'redeliveryExperience');
	$userId = I('post.userId', '', 'intval');//用户id
	$rechargeTime = I('post.time');//充值时间
	$rechargeAmount = I('post.amount');//充值金额
	$nodeName = I('post.node');//动作节点

	$operateExperience = new \Model\MarketingExperience();
	$awardNode = new \Model\AwardNode();
	$awardExperience = new \Model\AwardExperience();

	$nodeId = $awardNode->getNode($nodeName);
	if(!empty($nodeName)){
		$awardInfo = $awardExperience->filterUsefulExperience($nodeId);
	}

	//从用户中心 收到充值动作
	//1 判断在此之前 充值次数
	$postParams = array(
            'user_id' => $userId,
            'start_time'    => '',
            'end_time'    => $rechargeTime,
        );
	//$rechargeTimes = Common::jsonRpcApiCall((object)$postParams, 'getRechargeTimes', config('RPC_API.passport'));
	$rechargeTime = 2;

	//复投体验金只能获得一次
    $isExistExperience = $operateExperience->isExist($userId, $awardInfo['id']);

	if($rechargeTime == 2 && empty($isExistExperience)){
		$experienceInfo = array(
			'id' 	     => $awardInfo['id'],
			'title'      => $awardInfo['title'],
			'amount'     => $rechargeAmount,
			'days'       => $awardInfo['days'],
			'limit_desc' => $awardInfo['limit_desc'],
			'amount_type'=> $awardInfo['amount_type'],
			);
		$addres = $operateExperience -> addExperienceForUser($userId,$experienceInfo);
		if($addres){
			//通知用户中心发放体验金 
			$post = array(
				'status' => 'waiting', 
				'data'   => $addres,
				);
			Common::jsonRpcApiCall((object)$post, 'extendExperienceFromOperate', config('RPC_API.passport'));
		}
		logs($addres);
	}
}