<?php
namespace Model;
class MarketingActivity extends Model
{
    const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;

    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_activity');
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
        $this->where($where)->upd(['status' => $status]);
        return $this->where($where)->get()->rowArr();
    }

    public function activityList($page)
    {
        // $start = intval(($page - 1) * C('PAGE_SIZE'));
        $nowtime = date("Y-m-d H:i:s");
        return $this->fields('id, title, img_url, link_url, start_time, end_time,check_login, desc')
            ->where("`is_del` = 0 and `status` = 1 ")
            ->orderby("sort ASC")
            // ->limit($start, C('PAGE_SIZE'))
            ->get()->resultArr();
    }

    public function getUsefulTimeByName($name){
        return $this->where("`is_del` = 0 and `status` = 1 and `activity_name` = '{$name}'")
            ->get()->rowArr();
    }
    
    public function getUsefulActivityByName($activity_name){
        return $this->where("`is_del` = 0 and `status` = 1 and `activity_name` = '{$activity_name}'")
            ->get()->rowArr();
    }
}