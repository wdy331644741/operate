<?php
namespace Model;

class MarketingCheckin extends Model {

    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_checkin');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function isChecked($userId)
    {
        $today = date("Y-m-d");
        $checkInfo = $this->getCheckInfoByDate($userId, $today);
        if (empty($checkInfo)) {
            return false;
        }

        return true;
    }

    public function checkIn($userId)
    {
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        $yesterdayCheckInfo = $this->getCheckInfoByDate($userId, $yesterday);
        $data = array(
            'user_id'     => $userId,
            'create_time' => date('Y-m-d H:i:s')
        );
        $data['continue_days'] = empty($yesterdayCheckInfo) ? 1 : $yesterdayCheckInfo['continue_days'];

        return $this->add($data);
    }

    public function getCheckInfoByDate($userId, $date)
    {
        $dateStart = $date . " 00:00:00";
        $dateEnd = $date . " 23:59:59";

        return $this->where("`user_id` = {$userId} and `create_time` >= '{$dateStart}' and `create_time` < '{$dateEnd}'")
            ->get()->rowArr();
    }

}