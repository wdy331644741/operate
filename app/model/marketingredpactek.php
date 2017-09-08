<?php
namespace Model;
use App\service\exception\AllErrorException;
class MarketingRedpactek extends Model
{	
	const PRE_IS_USED = 0;
	const IS_USED = 1;
    const PRE_IS_ACTIVATE = 0;
    const IS_ACTIVATE = 0;
    const REDPACKET_TYPE_DEFAULT = 1;

    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_redpactek');
        if ($pkVal)
            $this->initArData($pkVal);
    }
    //领取红包id  他邀请的人id  节点id
    public function giveUserRedPacket($accept_userid,$userId,$nodeId){
    	$redPacketInfo = new AwardRedpacket();
    	$awardRedPacket = $redPacketInfo->getAwardInfo($nodeId);
		$repeat = $awardRedPacket['repeat'];//是否可以重复发放该红包(同一个userid)
		$day_repeat = $awardRedPacket['day_repeat'];//当天是否重复发放

		if($day_repeat == 0){
			$havedRedpacketToday = $this->getRedpacketByUseridDate($accept_userid,date("Y-m-d"));
			if(conut($havedRedpacketToday) > 1){

			}
		}
		$max_counts = $repeat == 1?(int)$awardRedPacket['max_counts']:1;//最大领取次数

		//查找该用户名下已有的红包数量
		$havedRedpacket = $this->getRedpacketByUserid($accept_userid);

		if(count($havedRedpacket) >= $max_counts)
			throw new AllErrorException(AllErrorException::REDPACKET_EXCEED_MAX_LIMIT);

    	$inseertRes = $this->insertRedPacketData($accept_userid,$userId,$awardRedPacket);//add data
    	if(!$inseertRes)//添加失败
    		throw new AllErrorException(AllErrorException::REDPACKET_INSERT_FALUSE);
    	$inseertRes['award'] = $awardRedPacket;//附带上红包配置
    	return $inseertRes;
    }

    public function changeRedPacketIsused($uuid,$is_used_status){
    	$changeArr = [];
    	if($is_used_status == 1){
    		$changeArr = [self::PRE_IS_USED => self::IS_USED];//从0->1
    	}elseif($is_used_status == 0){
    		$changeArr = [self::IS_USED => self::PRE_IS_USED];//从1->0
    	}
    	return $this->updateUserRedPacketIsused($uuid,$changeArr);
    }

    //查找该用户下所有已发放的红包
    private function getRedpacketByUserid($userId,$is_used = 1){
    	return $this->where(['accept_userid'=>$userId , 'is_used'=>$is_used])->get()->resultArr();
    }

    private function getRedpacketByUseridDate($userId,$date,$is_used = 1){
    	$timeStart = $date." 00:00:00";
    	$timeEnd = $date." 23:59:59";
    	return $this->where("accept_userid = {$userId} AND create_time >= '{$timeStart}' AND create_time <= '{$timeEnd}' AND is_used = {$is_used}")->get()->resultArr();
    }
    //给用户添加记录-内部方法
    private function insertRedPacketData($accept_userid,$userId, $awardInfo)
    {   
        $data = array( 
            'accept_userid'   => $accept_userid,
            'uuid'            => create_guid(),
            'source_id'       => $awardInfo['id'],
            'source_name'     => $awardInfo['title'],
            'amount'          => $awardInfo['amount'],
            'usetime_start'   => $awardInfo['usetime_start'], 
            'usetime_end'     => $awardInfo['usetime_end'], 
            'type'            => self::REDPACKET_TYPE_DEFAULT,
            'limit_desc'      => $awardInfo['limit_desc'],
            'effective_start' => '',//用户点击使用的时间 
            'is_used'         => self::PRE_IS_USED,
            'is_activate'     => self::PRE_IS_ACTIVATE,
            'create_time'     => date('Y-m-d H:i:s'),
            'update_time'     => date('Y-m-d H:i:s'),
            'be_invite_id'    => $userId,
        ); 
 
        $res = $this->add($data);
        if ($res) { 
            $data['id'] = $res; 
 
            return $data; 
        } 
 
        // return $data;
        return false;
    }

    //修改is_used字段状态
    private function updateUserRedPacketIsused($uuid,$arr){
    	return $this->where(['uuid'=>$uuid , 'is_used'=> 0])->upd(['is_used'=>1]);
    }


}