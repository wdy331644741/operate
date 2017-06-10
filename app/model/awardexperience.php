<?php
namespace Model;

class AwardExperience extends Model
{

    const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;
    const TYPE_NORMAL = 0;
    const TYPE_RAND = 1;

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
    public function getDetail($id)
    {
        return $this->where("`id` = {$id}")->get()->rowArr();
    }

    public function switchStausById($id)
    {
        $where = ['id' => $id];
        $row = $this->where($where)->get()->row();
        if ($row->status == self::STATUS_TRUE)
            $status = self::STATUS_FALSE;
        else
            $status = self::STATUS_TRUE;
    }
    
    public function getAwardExperienceByName($experience_name){
        return $this->where("`experience_name` = '{$experience_name}'")
            ->get()->rowArr();

        return $this->where($where)->upd(['status' => $status]);
    }

    /**
     * 获取有效的体验金配置id
     * @param $nodeIds
     * @return mixed
     */
    public function getByLimitNodes($nodeIds)
    {
        $nowTime = date("Y-m-d H:i:s");
        $fields = "id";
        return $this->fields($fields)
            ->where("`effective_end` > '{$nowTime}' and status = 1 and is_del = 0")
            ->whereIn('limit_node', $nodeIds)
            ->orderby("id DESC")
            ->get()->resultArr();
    }

}