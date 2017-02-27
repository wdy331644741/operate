<?php
namespace Model;

class AwardExperience extends Model {

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

    public function getDetail($id)
    {
        return $this->where("`id` = {$id}")->get()->rowArr();
    }

    public function filterUsefulExperience($nodeId)
    {
        $nowTime = date("Y-m-d H:i:s");

        return $this->where("`limit_node` = {$nodeId} and `effective_end` > '{$nowTime}' and status = 1 and is_del = 0")
            ->orderby("id DESC")
            ->get()->rowArr();
    }

    public function switchStausById($id)
    {
        $where = ['id' => $id];
        $row = $this->where($where)->get()->row();
        if ($row->status == self::STATUS_TRUE)
            $status = self::STATUS_FALSE;
        else
            $status = self::STATUS_TRUE;

        return $this->where($where)->upd(['status' => $status]);
    }
}