<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common;


/**
 * 新手活动 注册触发  立即激活注册体验金、预发放（绑卡体验金、首投体验金、10-40元红包）
 * @pageroute
 */
function register(){
	$userId = I('post.user_id', '', 'intval');
	$time = I('post.time', '');
	//如果时间在活动之外
	$activity_name = 'new_bird'; //活动标示
	// $activityModel = new \Model\MarketingActivity();
	// $activityInfo = $activityModel->getUsefulActivityByName($activity_name);
	$activityInfo = getConfig($activity_name);
	if($activityInfo['start_time'] > $time || $activityInfo['end_time'] < $time)
		exit("未在活动时间范围内");
	//待激活的体验金  effective时间段
	$effective_start = date('Y-m-d H:i:s',strtotime($activityInfo['end_time']) - 1 );

	$activiteExp = 'newbird_activite_exprience';//节点名称 	新手活动注册待激活体验金
	$activiteAvailable = 'newbird_available';//新手活动注册立即获得
	$activiteRed = 'newbird_activite_redpacket';
    $nodeModel = new \Model\AwardNode();

    $activiteExpId = $nodeModel->getNode($activiteExp);
    $activiteAvailableId = $nodeModel->getNode($activiteAvailable);
    $activiteRedId = $nodeModel->getNode($activiteRed);
    try {

        experience($userId, $activiteAvailableId,true,array() );//发放5w x 3小时 和66 x 365天
        experience($userId, $activiteExpId,false,['effective_start'=> $effective_start ,'effective_end'=> $activityInfo['end_time']]);//绑卡 首投
        redpactek($userId, $activiteRedId);
        //coupon($userId, $nodeId);//注册加息券
        
    } catch (\Exception $e) {
        $msg = "用户ID: {$userId} 触发：{$type}，发放入账失败：" . PHP_EOL;
        $msg .= "接口错误码：{$e->getCode()}, 错误信息：{$e->getMessage()}" . PHP_EOL;
        logs($msg, 'trigger');

        echo $msg;
    }
}

/**
 * 激活红包
 * @pageroute
 */
function demandbuy(){
	$userId = I('post.user_id', '', 'intval');//充值定期用户id
    $time = I('post.buy_time');//充值时间
    $rechargeAmount = I('post.amount');//充值金额
    $fromUserid = I('post.from_id');//邀请该用户的id
    $demand_count = I('post.demand_count');//投资定期次数
    $is_new = I('post.is_new');//是否是新用户
    $porject_day = I('post.porject_day');//标的天数

    $activityInfo = getConfig($activity_name);
	if($activityInfo['start_time'] > $time || $activityInfo['end_time'] < $time)
		exit("未在活动时间范围内");

    $activiteRed = 'newbird_activite_redpacket';
    try{
    	if($is_new){
    		//投资定期360天产品 40
    		//投资定期90天产品 30
    		//投资定期30天产品，投资金额≧4000元 20
    		//投资定期30天产品，投资金额≧2000元 10
    		switch ($porject_day) {
    			case 360:
    				$redpacket = 'register_redpacket_forty';
    				break;
    			case 90:
    				$redpacket = 'register_redpacket_thirty';
					break;
				case 30:
					if($rechargeAmount >= 4000)
    					$redpacket = 'register_redpacket_forty';
    				elseif($rechargeAmount >= 2000)
    					$redpacket = 'register_redpacket_ten';
					break;

    			default:
    				# code...
    				break;
    		}

    		//激活红包
    		activiteRedPacket($activiteRed,$userId,$redpacket);
    	}else{
    		throw new \Exception('不是新用户',-72322);
    	}

    }catch(\Exception $e){
    	$msg = "用户ID: {$userId} 触发：激活红包失败：" . PHP_EOL;
        $msg .= "接口错误码：{$e->getCode()}, 错误信息：{$e->getMessage()}" . PHP_EOL;
        logs($msg, 'trigger');

        echo $msg;
    }

}

/**
 * 新手活动 绑卡激活 绑卡体验金
 * @pageroute
 */
function bandcard(){
	$userId = I('post.user_id', '', 'intval');
	$time = I('post.time', '');
	$node_name = I('post.node_name', '');//bindcard

	//如果时间在活动之外
	$activity_name = 'new_bird'; //活动标示
	$activityInfo = getConfig($activity_name);
	if($activityInfo['start_time'] > $time || $activityInfo['end_time'] < $time)
		exit("未在活动时间范围内");

	try {
		$sourceName = "register_experience_bandcard";
	    $awardExperienceModel = new \Model\AwardExperience();
	    $ExperienceModel = new \Model\MarketingExperience(); 

	    $sourceId = $awardExperienceModel->getAwardExperienceByName($sourceName);
        $activeExpData = $ExperienceModel->isExist($userId,$sourceId['id']);
        if(empty($activeExpData))
        	throw new \Exception("该用户不存在待激活绑卡体验金", 75521);

        $activePost = array(
						'uuid' => $activeExpData['uuid'],
						'status' => 1
						);
		Common::jsonRpcApiCall((object)$activePost, 'activateExperienceGoldToUser', config('RPC_API.passport'));
		$ExperienceModel->updateStatusOfUse($expId);
		exit($userId."激活".$activeExpData['source_name']."成功");

    } catch (\Exception $e) {
        $msg = "用户ID: {$userId} 触发：，发放入账失败：" . PHP_EOL;
        $msg .= "接口错误码：{$e->getCode()}, 错误信息：{$e->getMessage()}" . PHP_EOL;
        logs($msg, 'trigger');

        echo $msg;
    }

}

/**
 * 新手活动 第一次充值
 * @pageroute
 */
function recharge(){
	try{
		$userId = I('post.userId', '', 'intval');//用户id
		$time = I('post.time');//充值时间
		$rechargeAmount = I('post.amount');//充值金额
		$rechargeCounts = I('post.counts');//充值次数

		if(empty($userId) || empty($time) || empty($rechargeAmount) || empty($rechargeCounts) )
			throw new \Exception("参数错误", 75521);
		if($rechargeCounts > 1){
			exit("充值次数大于1，不给首投体验金");
		}
		$activity_name = 'new_bird'; //活动标示
		$activityInfo = getConfig($activity_name);
		if($activityInfo['start_time'] > $time || $activityInfo['end_time'] < $time)
			exit("未在活动时间范围内");

	
		$sourceName = "register_experience_frist";
	    $awardExperienceModel = new \Model\AwardExperience();
	    $ExperienceModel = new \Model\MarketingExperience(); 

	    $sourceId = $awardExperienceModel->getAwardExperienceByName($sourceName);
        $activeExpData = $ExperienceModel->isExist($userId,$sourceId['id']);
        if(empty($activeExpData))
        	throw new \Exception("该用户不存在待激活绑卡体验金", 75521);
        $effective_start = strtotime($time) + 15*86400;
        $effective_start = date('Y-m-d H:i:s',$effective_start);
        $effective_end = strtotime($time) + 15*86400 + $sourceId['days']*86400 + $sourceId['hours']*3600;
        $effective_end = date('Y-m-d H:i:s',$effective_end);
        $activePost = array(
						'uuid' => $activeExpData['uuid'],
						'status' => 1,
						'effective_start' => $effective_start,
						'effective_end' => $effective_end,
						);
		Common::jsonRpcApiCall((object)$activePost, 'activateExperienceGoldToUser', config('RPC_API.passport'));
		$ExperienceModel->updateStatusOfUse($activeExpData['id'],['effective_start' => $effective_start,'effective_end' => $effective_end]);
		exit($userId."激活".$activeExpData['source_name']."成功");
	}catch (\Exception $e) {
        $msg = "用户ID: {$userId} 触发：" . PHP_EOL;
        $msg .= "接口错误码：{$e->getCode()}, 错误信息：{$e->getMessage()}" . PHP_EOL;
        logs($msg, 'trigger');

        echo $msg;
    }

	// $nodeName = I('post.node');//动作节点
}

/**
 * 新手活动 提现 需要disable  首投已经激活的体验金
 * @pageroute
 */
function withdraw(){
	try{
		$userId = I('post.user_id', '', 'intval');//用户id
		$time = I('post.time');//充值时间
		$withdrawAmount = I('post.amount');//充值金额
		$withdrawAmountTotal = I('post.total_amount');//累计本金

		if(empty($userId) || empty($time) || empty($withdrawAmount) || empty($withdrawAmountTotal) )
			throw new \Exception("参数错误", 75521);

		$sourceName = "register_experience_frist";
	    $awardExperienceModel = new \Model\AwardExperience();
	    $ExperienceModel = new \Model\MarketingExperience(); 

	    $sourceId = $awardExperienceModel->getAwardExperienceByName($sourceName);
        $activeExpData = $ExperienceModel->isExist($userId,$sourceId['id']);
        if($activeExpData['is_activate'] == 0)
        	throw new \Exception("新手福利-用户首投体验金还没有激活", 75521);
        
        //在激活的体验金 开始之前提现
        if($activeExpData['effective_start'] >= $time && $activeExpData['effective_end'] >= $time){
        	//disableExperienceGlodForUser ->token 体验金
        	$disPost = [
        		'token' => $activeExpData['uuid'],
        	];
        	$res = Common::jsonRpcApiCall((object)$disPost, 'disableExperienceGlodForUser', config('RPC_API.passport'));
        	var_dump($res);exit;
        }else{
        	exit("提现时间超出规则");
        }

	}catch (\Exception $e) {
        $msg = "用户ID: {$userId} 触发：" . PHP_EOL;
        $msg .= "接口错误码：{$e->getCode()}, 错误信息：{$e->getMessage()}" . PHP_EOL;
        logs($msg, 'trigger');

        echo $msg;
    }
	
}

function redpactek($userId,$nodeId){
	$marketingRedpactekModel = new \Model\MarketingRedpactek();
    $run = $marketingRedpactekModel->_giveUserRedPacket($userId,0,$nodeId,2,0);//发放红包 邀请人为0 红包类型为2 激活0

	foreach ($run as $key => $value) {
		//preSendRedPackToUser 请求用户中心接口
	    unset($value['id']);
	    unset($value['award']);//相关红包的配置参数
	    $proPost = $value;
	    $res = @Common::jsonRpcApiCall((object)$proPost, 'preSendRedPackToUser', config('RPC_API.passport'));
	}
    
}



function activiteRedPacket($nodeName,$userId,$redpacket){
	$awardRedModel = new \Model\AwardRedpacket();
	$RedModel = new \Model\MarketingRedpactek();

    $awardInfo = $awardRedModel->getRedpacketInfoByName($redpacket);
    $sourceId = $awardInfo['id'];

    $activeData = $RedModel->isExecuteBeactivated($userId,$sourceId,0);

    //激活红包
    $activePost = [
        'uuid' => $activeData['uuid'],
    ];
    $activeRes = Common::jsonRpcApiCall((object)$activePost, 'activeRedPackToUser', config('RPC_API.passport'));
    if($activeRes['result']){
            $RedModel->changeRedPacketIsused($activeData['uuid'],1);
        }
    exit($userId."激活".$activeData['source_name']."成功");
}

//发放体验金
function experience($userId,$nodeId,$activate=true,$effective = array() ){
	$awardExperience = new \Model\AwardExperience();//体验金配置
	$operateExperience = new \Model\MarketingExperience();

	$awardExpInfo = $awardExperience->_filterUsefulExperience($nodeId);
	// var_dump($awardExpInfo);exit;
	if(empty($awardExpInfo))
		throw new \Exception("节点下未配置体验金", 74232);
	foreach ($awardExpInfo as $key => $value) {
		$isExistExperience = $operateExperience->isExist($userId, $value['id']);
	    if(empty($isExistExperience)){
		   	//***************发放体验金************************
		   	if(!empty($effective)){
		   		$params_rate_time = empty($value['days'])?"hours":"days";
		   		$experienceInfo = array(
		   			'uuid'            => create_guid(),
	                'source_id'       => $value['id'],
	                'source_name'     => $value['title'],
	                'amount'          => $value['amount'],
	                'effective_start' => $effective['effective_start'],
	                'effective_end'   => $effective['effective_end'],
	                'continuous_'.$params_rate_time => $value[$params_rate_time],
	                'limit_desc'      => $value['limit_desc'],
	                'create_time'     => date('Y-m-d H:i:s'),
	                'is_use'          => 1
		   			);
		   	}else{
		   		$experienceInfo = array(
				'id' 	     => $value['id'],
				'title'      => $value['title'],
				'amount'     => $value['amount'],
				'days'       => $value['days'],//计息时长
				'hours'      => $value['hours'],
				'limit_desc' => $value['limit_desc'],
				'amount_type'=> $value['amount_type'],
	            'is_use'     => 1
				);
		   	}
			
			$addExperienceRes = $operateExperience -> addExperienceForUser($userId,$experienceInfo);
			$expId = $addExperienceRes['id'];
			unset($addExperienceRes['id']);
			//通知用户中心 预发放体验金 
			if($addExperienceRes){
				$preSend = array(
					'expAward'   => $addExperienceRes,
					);
				$resRpc = Common::jsonRpcApiCall((object)$preSend, 'preSendExperienceGoldToUser', config('RPC_API.passport'));
				//$resRpc = true;
				if($resRpc && $activate){
					$activePost = array(
						'uuid' => $addExperienceRes['uuid'],
						'status' => 1
						);
					// var_dump($activePost);
					Common::jsonRpcApiCall((object)$activePost, 'activateExperienceGoldToUser', config('RPC_API.passport'));
					$operateExperience->updateStatusOfUse($expId);
				}
			}
			//****************************************************		
			sleep(2);
	    }
	}
    

}

function getConfig($key){
	$redis = getReidsInstance();
    $activityInfo = $redis->hget('operate_gloab_conf',$key);
    $activityInfo = json_decode($activityInfo,true);
    if(empty($activityInfo)){
    	$activityModel = new \Model\MarketingActivity();
		$activityInfo = $activityModel->getUsefulActivityByName($key);
    }
    return $activityInfo;

}