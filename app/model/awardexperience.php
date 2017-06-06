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

    public function filterUsefulExperience($nodeId)
    {
        $nowTime = date("Y-m-d H:i:s");
        $res = $this->where("`limit_node` = {$nodeId} and `effective_end` > '{$nowTime}' and status = 1 and is_del = 0")
            ->orderby("id DESC")
            ->get()->rowArr();
        return $res;
    }

    
    public function getAwardExperienceByName($experience_name){
        return $this->where("`experience_name` = '{$experience_name}'")
            ->get()->rowArr();

    }
}