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
}