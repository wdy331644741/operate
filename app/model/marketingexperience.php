<?php
namespace Model;

class MarketingExperience extends Model {

    const TYPE_RANDOM = 1;

    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_experience');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    //获取该天 所有预发放体验金的用户
    public function getExpUserByTime($time){
        $start_time = $time.' 00:00:00';
        $end_time = $time.' 23:59:59';
        return $this->fields('id,user_id',false)
            ->where("`create_time` >= '{$start_time}' and `create_time` <= '{$end_time}' and `is_activate` = '0' and `is_use` = 0")
            ->get()->resultArr();
    }

    //判断是否存在该条记录
    public function isExist($userId,$sourceId){
        $result = $this->where("`user_id` = {$userId} and `source_id` = {$sourceId}")
            ->orderby("id DESC")
            ->get()
            ->rowArr();
        return $result;
    }

    //给用户添加记录
    public function addExperienceForUser($userId, $expInfo, $laterdays = '')
    {
        if (empty($expInfo)) {
            return false;
        }
        if(empty($laterdays)){
            $data = $this->getExperienceDataByType($expInfo);
        }else{
            $data = $this->getExperienceDataByTypeLater($expInfo,$laterdays);
        }
        

        $data['user_id'] = $userId;
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
        return $this->where("`id` = {$id} and `is_use` = 0 and `is_activate` = 0 ")
            ->upd(array('is_use' => 1, 'is_activate' => 1 ,'update_time' => date('Y-m-d H:i:s')));
    }

    //获取用户体验金列表
    public function getExperienceList($userId)
    {
        return $this->fields('source_name, amount, effective_start, effective_end')
            ->where("`user_id` = {$userId} and `is_use` = 1")
            ->get()->resultArr();
    }

    //获取计息中的体验金总额
    public function getUseExperienceAmount($userId)
    {
        $result = $this->fields("SUM(amount) as amount", false)
            ->where("`user_id` = {$userId} and `is_use` = 1 and `effective_end` > NOW()")
            ->get()->rowArr();

        if (empty($result)) {
            return '0.00';
        }

        return $result['amount'];
    }

    protected function getExperienceDataByType($experienceInfo)
    {
        if ($experienceInfo['amount_type'] == self::TYPE_RANDOM) {
            $experienceInfo['amount'] = mt_rand($experienceInfo['min_amount'], $experienceInfo['max_amount']);
        }
        if(isset($experienceInfo['days']) && !empty($experienceInfo['days']) ){

            return array(
                'uuid'            => create_guid(),
                'source_id'       => $experienceInfo['id'],
                'source_name'     => $experienceInfo['title'],
                'amount'          => $experienceInfo['amount'],
                'effective_start' => date('Y-m-d H:i:s'),
                'effective_end'   => date('Y-m-d H:i:s', time() + $experienceInfo['days'] * DAYS_SECONDS),
                'continuous_days' => $experienceInfo['days'],
                'limit_desc'      => $experienceInfo['limit_desc'],
                'create_time'     => date('Y-m-d H:i:s')
            );
        }else if(!empty($experienceInfo['hours']) && $experienceInfo['days'] == 0){
            return array(
                'uuid'            => create_guid(),
                'source_id'       => $experienceInfo['id'],
                'source_name'     => $experienceInfo['title'],
                'amount'          => $experienceInfo['amount'],
                'effective_start' => date('Y-m-d H:i:s'),
                'effective_end'   => date('Y-m-d H:i:s', time() + $experienceInfo['hours'] * 3600),
                'continuous_hours' => $experienceInfo['hours'],
                'limit_desc'      => $experienceInfo['limit_desc'],
                'create_time'     => date('Y-m-d H:i:s')
            );
        }
    }

    protected function getExperienceDataByTypeLater($experienceInfo,$laterdays)
    {
        if ($experienceInfo['amount_type'] == self::TYPE_RANDOM) {
            $experienceInfo['amount'] = mt_rand($experienceInfo['min_amount'], $experienceInfo['max_amount']);
        }

        return array(
            'uuid'            => create_guid(),
            'source_id'       => $experienceInfo['id'],
            'source_name'     => $experienceInfo['title'],
            'amount'          => $experienceInfo['amount'],
            'effective_start' => date('Y-m-d H:i:s', time() + $laterdays * DAYS_SECONDS),
            'effective_end'   => date('Y-m-d H:i:s', time() + ($laterdays+$experienceInfo['days']) * DAYS_SECONDS),
            'continuous_days' => $experienceInfo['days'],
            'limit_desc'      => $experienceInfo['limit_desc'],
            'create_time'     => date('Y-m-d H:i:s')
        );
    }

    protected function getExperienceDataByTypeLater($experienceInfo,$laterdays)
    {
        if ($experienceInfo['amount_type'] == self::TYPE_RANDOM) {
            $experienceInfo['amount'] = mt_rand($experienceInfo['min_amount'], $experienceInfo['max_amount']);
        }

        return array(
            'uuid'            => create_guid(),
            'source_id'       => $experienceInfo['id'],
            'source_name'     => $experienceInfo['title'],
            'amount'          => $experienceInfo['amount'],
            'effective_start' => date('Y-m-d H:i:s', time() + $laterdays * DAYS_SECONDS),
            'effective_end'   => date('Y-m-d H:i:s', time() + ($laterdays+$experienceInfo['days']) * DAYS_SECONDS),
            'continuous_days' => $experienceInfo['days'],
            'limit_desc'      => $experienceInfo['limit_desc'],
            'create_time'     => date('Y-m-d H:i:s')
        );
    }
}