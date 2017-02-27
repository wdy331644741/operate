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

    //给用户添加记录
    public function addExperienceForUser($userId, $expInfo)
    {
        if (empty($expInfo)) {
            return false;
        }
        $data = $this->getExperienceDataByType($expInfo);

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
        return $this->where("`id` = {$id} and `is_use` = 0")
            ->upd(array('is_use' => 1, 'update_time' => date('Y-m-d H:i:s')));
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
    }

}