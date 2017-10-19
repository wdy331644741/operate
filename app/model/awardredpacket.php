<?php
namespace Model;
class AwardRedpacket extends Model
{
	const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;
    const TYPE_NORMAL = 0;
    const TYPE_RAND = 1;
    public function __construct($pkVal = '')
    {
        parent::__construct('award_redpacket');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function getAwardInfo($nodeId){
        return $this->where(" limit_node = {$nodeId} AND is_del = 0")->get()->rowArr();
    }

    public function getAwardInfoArr($nodeId){
        return $this->where(" limit_node = {$nodeId} AND is_del = 0")->get()->resultArr();
    }

    public function switchStausById($id)
    {
        $where = ['id' => $id];
        $row = $this->where($where)->get()->row();
        if ($row->status == self::STATUS_TRUE)
            $status = self::STATUS_FALSE;
        else
            $status = self::STATUS_TRUE;

        return $this->where($where)->upd(['status' => $status]);
    }

    public function getRedpacketInfoByName($name){
        return $this->where("redpacket_name = '{$name}' AND status = 1 AND is_del = 0")->get()->rowArr();
    }
}