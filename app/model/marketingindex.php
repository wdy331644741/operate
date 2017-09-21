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

    //是否有已经 存在的 展示的 默认的
    public function hasDefault(){
        return $this->where(['is_del'=>0 , 'pos'=> 1 ,'status'=> 1])->get()->rowArr();

    }

    public function getIndexList(){
        return $this->where(['is_del'=>0])->get()->resultArr();
    }

    public function delById($id){
        $where = ['id' => $id];
        return $this->where($where)->upd(['is_del' => 1]);
    }


    public function getDefaultSlogen(){
    	return $this->where(['status'=>1,'pos'=>1,'is_del'=>0])->get()->rowArr();

    }

    public function getMomentSlogen(){
    	return true;
    }
}