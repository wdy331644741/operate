<?php
namespace Model;
class AwardHandRecord extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('award_hand_record');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function getListByAwardId($id)
    {
        return $this->where(['award_extend_id'=>$id])->get()->resultArr();
    }
}