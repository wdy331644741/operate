<?php
namespace Model;
class MarketingWithdrawcoupon extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_withdrawcoupon');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    //更新用户提现劵状态
    public function updateStatusOfUse($id){
        return $this->where("`id` = {$id} and `is_use` = 1 and `is_activate` = 0 ")
            ->upd(array('is_activate' => 1 ,'update_time' => date('Y-m-d H:i:s')));
    }

    //获取所有 发放过提现劵的用户
    public function getCouponUserByTime($time){
        $start_time = $time.' 00:00:00';
        $end_time = $time.' 23:59:59';
        return $this->fields('uuid,id,user_id,create_time',false)
            ->where("`create_time` >= '{$start_time}' and `create_time` <= '{$end_time}' and `is_activate` = '0' and `is_use` = 1")
            ->get()->resultArr();
    }

    //是否存在该记录
    public function isExist($userId,$sourceId){
        $result = $this->where("`user_id` = {$userId} and `source_id` = {$sourceId}")
            ->orderby("id DESC")
            ->get()
            ->rowArr();
        return $result;
    }

    //给用户添加记录
    public function addWithdrawForUser($userId, $awardInfo,$laterdays=0){
    	$data = array(
            'user_id'         => $userId,
            'uuid'            => create_guid(),
            'source_id'       => $awardInfo['id'],
            'source_name'     => $awardInfo['title'],
            'effective_start' => date('Y-m-d H:i:s',time() + $laterdays * DAYS_SECONDS),
            'effective_end'   => $awardInfo['effective_end'],
            // 'remain_times' => $awardInfo['remain_times'],
            'limit_desc'      => $awardInfo['limit_desc'],
            'create_time'     => date('Y-m-d H:i:s'),
            'update_time'     => date('Y-m-d H:i:s'),
            'is_use'          => 1
        );

        $res = $this->add($data);
        if ($res) {
            $data['id'] = $res;
            return $data;
        }

        return false;
    }
}