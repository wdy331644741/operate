<?php
namespace Model;

class MarketingInterestcoupon extends Model {

    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_interestcoupon');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    //是否有其他 阶梯加息已经计息的加息劵
    public function isOtherActivateExist($userId){
        $result = $this->where("`user_id` = {$userId} and `source_id` in (10,11) and `status` = 1")
        ->orderby("id DESC")
        ->get()
        ->resultArr();
        $result = array_column($result,NULL,'id');
        return $result;
    }

    //是否存已经计息的记录
    //array中包含预发放的加息劵，
    public function isActivateExist($userId,$sourceId){
        if(is_array($sourceId)){
            $sourceIdStr = implode(',', $sourceId);
            $result = $this->where("`user_id` = {$userId} and `source_id` in ({$sourceIdStr}) and `status` = 1")
            ->orderby("id DESC")
            ->get()
            ->resultArr();
        }else{
            $result = $this->where("`user_id` = {$userId} and `source_id` = {$sourceId} and `is_activate` = 1 and `status` = 1")
            ->orderby("id DESC")
            ->get()
            ->rowArr();
        }
        
        return $result;
    }

    //是否存在 预发送记录
    public function isExist($userId,$sourceId){
        $result = $this->fields("id,uuid", false)
            ->where("`user_id` = {$userId} and `source_id` = {$sourceId} and `status` = 1")
            ->orderby("id DESC")
            ->get()
            ->rowArr();
        return $result;
    }

    //获取用户所有加息券
    public function getListByUserid($userId)
    {
        return $this->fields('id, source_name, rate, effective_start, effective_end, continuous_days, is_use')
            ->orderby("rate desc, effective_end asc")
            ->where("`user_id` = {$userId}")
            ->get()->resultArr();
    }

    //获取加息券详情
    public function couponInfo($couponId)
    {
        return $this->where("`id` = {$couponId}")->get()->rowArr();
    }

    //给用户添加记录
    public function addCouponForUser($userId, $awardInfo)
    {   
        $data = array(
            'user_id'         => $userId,
            'uuid'            => create_guid(),
            'source_id'       => $awardInfo['id'],
            'source_name'     => $awardInfo['title'],
            // 'usetime_start'   => $awardInfo['effective_start'],
            // 'usetime_end'     => $awardInfo['usetime_end'],
            'usetime_start'   => date('Y-m-d H:i:s'),
            'usetime_end'     => date('Y-m-d H:i:s', time() + $awardInfo['effective_days'] * DAYS_SECONDS),//加息券可使用的有效天数（复投活动）
            'rate'            => $awardInfo['rate'],
            'effective_start' => date('Y-m-d H:i:s'),// 6月5号紧急使用，下一版需要调整
            'effective_end'   => date('Y-m-d H:i:s', time() + $awardInfo['effective_days'] * DAYS_SECONDS),// 6月5号紧急使用，下一版需要调整
            // 'effective_start' => '1970-01-01 00:00:00',
            // 'effective_end'   => '1970-01-01 00:00:00',
            'continuous_days' => $awardInfo['days'],
            'limit_desc'      => $awardInfo['limit_desc'],
            'create_time'     => date('Y-m-d H:i:s'),
            'update_time'     => date('Y-m-d H:i:s'),
            'is_use'          => $awardInfo['is_use']

        );
        if($awardInfo['days'] == 0){
            $data['effective_end'] = $awardInfo['effective_end'];
        }
        $res = $this->add($data);
        if ($res) {
            $data['id'] = $res;

            return $data;
        }

        return false;
    }

    //更新使用状态
    public function updateStatusOfUse($id)
    {
        return $this->where("`id` = {$id} and `is_use` = 0")
            ->upd(array('is_use' => 1, 'update_time' => date('Y-m-d H:i:s')));
    }

    //更新激活状态及时间
    public function updateActivate($uuid,$activate=1,$status=1,$effective_start='',$effective_end=''){
        if(!empty($effective_start) && !empty($effective_end)){
            $this->where("`uuid` = '{$uuid}'")
            ->upd(array('is_activate' => $activate,'status' => $status,'effective_start'=>$effective_start,'effective_end'=>$effective_end, 'update_time' => date('Y-m-d H:i:s')));
        }
        $ww = $this->where("`uuid` = '{$uuid}'")
            ->upd(array('is_activate' => $activate,'status' => $status, 'update_time' => date('Y-m-d H:i:s')));
    }

    //更新状态
    
    //获取一天内所有的数据
    public function getAllDataByDay($date,$couponId){
        $dateStartTime = $date." 00:00:00";
        $dateEndTime = $date." 23:59:59";
        $returnArr = $this->where("`effective_start` > '{$dateStartTime}' and `effective_start` < '{$dateEndTime}' and `status` = 1 and `is_activate` = 0 and `source_id` = {$couponId}")
                ->get()->resultArr();
        return $returnArr;
    }

    public function getActivateAndStatusData($userId){
        $qq = $this->where("`user_id` = {$userId} and `status` = 1 and `is_activate` = 1 and `source_id` in (10,11)")
            ->get()->resultArr();
            // logs($this->getLastQuery(),'22222222');
        return $qq;
    }
}