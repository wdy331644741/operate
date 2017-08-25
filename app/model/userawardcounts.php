<?php
namespace Model;

class UserAwardCounts extends Model
{

    public function __construct($pkVal = '')
    {
        parent::__construct('user_award_counts');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function getUserRecordByType($userId, $type)
    {
        return $this->where("user_id = '{$userId}' and `award_type` = '{$type}'")
            ->get()->rowArr();
    }

    public function getCountsByType($userId, $type)
    {
        $record = $this->where("user_id = '{$userId}' and `award_type` = '{$type}'")
            ->get()->rowArr();

        if (empty($record)) {
            return 0;
        }

        return $record['award_nums'];
    }

    public function increaseGrantCounts($userId, $type)
    {
        $record = $this->getUserRecordByType($userId, $type);
        if (empty($record)) {
            return $this->initRecord($userId, $type);
        }
        $numbers = $record['award_nums'] + 1;

        return $this->where("id = '{$record['id']}'")->upd(['award_nums' => $numbers]);
    }

    public function initRecord($userId, $type)
    {
        return $this->add(array(
            'user_id'    => $userId,
            'award_type' => $type,
            'award_nums' => 1
        ));
    }
}