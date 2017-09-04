<?php
namespace Model;
class MarketingRedpactek extends Model
{	
	const IS_USED = 1;
    const IS_ACTIVATE = 0;
    const REDPACKET_TYPE_DEFAULT = 1;

    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_redpactek');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function giveUserRedPacket($userId,$nodeId){
    	$redPacketInfo = new AwardRedpacket();
    	$awardRedPacket = $redPacketInfo->getAwardInfo($nodeId);
    	$inseertRes = $this->insertRedPacketData($userId,$awardRedPacket);
    	if(!$inseertRes)//添加失败
    		throw new Exception("insert redpacket false!", 7112);
    	$inseertRes['award'] = $awardRedPacket;//附带上红包配置
    	return $inseertRes;
    }
    //给用户添加记录-内部方法
    private function insertRedPacketData($userId, $awardInfo)
    {   
        $data = array( 
            'accept_userid'   => $userId,
            'uuid'            => create_guid(),
            'source_id'       => $awardInfo['id'],
            'source_name'     => $awardInfo['title'],
            'amount'          => $awardInfo['amount'],
            'usetime_start'   => $awardInfo['usetime_start'], 
            'usetime_end'     => $awardInfo['usetime_end'], 
            'type'            => self::REDPACKET_TYPE_DEFAULT,
            'limit_desc'      => $awardInfo['limit_desc'],
            'effective_start' => '',//用户点击使用的时间 
            'is_used'         => self::IS_USED,
            'is_activate'     => self::IS_ACTIVATE,
            'create_time'     => date('Y-m-d H:i:s'),
            'update_time'     => date('Y-m-d H:i:s'),
        ); 
 
        $res = $this->add($data);
        if ($res) { 
            $data['id'] = $res; 
 
            return $data; 
        } 
 
        // return $data;
        return false;
    } 
}