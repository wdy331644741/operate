<?php defined("__FRAMEWORKNAME__") or die("No permission to access!");
/**
 * @pageroute
 * 用户新增数据
 */
function index(){
    $framework = getFrameworkInstance();
    $userModel = new \Model\AuthUser(); //'create_time > ' .date('Y-m-d',(time()-86400*10)). ' AND create_time <='.date('Y-m-d')
    $where = 'create_time > '. '" ' .date('Y-m-d H:i:s',(time()-86400*10)) .' " '. ' AND  create_time <= '.'" ' .date('Y-m-d H:i:s').' "';
   // dump($where);die;
    $userDayNums=$userModel->where($where)->countNums();
    //dump($data);die;

    //$awardTable = ['award_interestcoupon','award_experience','config_earnings','marketing_activity'];
    //$remindDays = 7;
	$remindInfo = array(
		'interestcoupon' =>array(
			'show'       =>'id,title',
			'table'      => 'award_interestcoupon',
			'desc'       => '加息券',
			'filed'      => 'effective_end',
			'remindDays' => '7',
		),
		'experience'     =>array(
			'show'       =>'id,title',
			'table'      => 'award_experience',
			'desc'       => '体验金',
			'filed'      => 'effective_end',
			'remindDays' => '7',
		),
		'earnings'       =>array(
			'show'       =>'id,title',
			'table'      => 'config_earnings',
			'desc'       => '邀请收益、',
			'filed'      => 'end_time',
			'remindDays' => '7',
		),
		'activity'       =>array(
			'show'       =>'id,title',
			'table'      => 'marketing_activity',
			'desc'       => '活动',
			'filed'      => 'end_time',
			'remindDays' => '7',
		),
	);

	$tipArray = [];
	foreach ($remindInfo as $key => $value) {
		# code...
		$sql = "SELECT {$value['show']},DATEDIFF({$value['filed']},curdate()) AS remind from `{$value['table']}` WHERE DATEDIFF({$value['filed']},curdate())<{$value['remindDays']} AND DATEDIFF({$value['filed']},curdate())>0 AND `is_del` = 0 ORDER BY {$value['filed']} DESC";
		// echo $sql;
		$tipArray[$key] = $userModel->query($sql)->resultArr();
	}
	// var_export($tipArray);
	$framework->smarty->assign('tipArray',$tipArray);
    $framework->smarty->assign('userNum',$userDayNums);
    $framework->smarty->display('index/index.html');
}