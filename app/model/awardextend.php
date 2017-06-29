<?php
namespace Model;
class AwardExtend extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('award_extend');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}