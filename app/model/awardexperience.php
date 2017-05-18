<?php
namespace Model;
class AwardExperience extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('award_experience');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}