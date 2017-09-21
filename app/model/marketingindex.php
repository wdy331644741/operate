<?php
namespace Model;
class MarketingIndex extends Model
{
    const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;
    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_index');
        if ($pkVal)
            $this->initArData($pkVal);
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

    public function getDefaultSlogen(){
    	return $this->where(['status'=>1,'pos'=>1,'is_del'=>0])->get()->rowArr();

    }

    public function getMomentSlogen(){
    	return true;
    }
}