<?php
namespace Model;
class ConfigEarnings extends Model
{
    const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;

    public function __construct($pkVal = '')
    {
        parent::__construct('config_earnings');
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

    public function earningsList($page)
    {
        $start = intval(($page - 1) * C('PAGE_SIZE'));
        $nowtime = date("Y-m-d H:i:s");
        return $this->fields('id,title,max_amount,desc,start_date,end_date,is_del')
            ->where("`is_del` = 0 and `status` = 1 and `start_time` <= '{$nowtime}' and `end_time` > '{$nowtime}'")
            ->orderby("sort DESC")
            ->limit($start, C('PAGE_SIZE'))
            ->get()->resultArr();
    }

    public function getInfoByTitle($titile)
    {
        $where = ['title' => $titile];
        $row = $this->fields("id,title,start_time,end_time", false)
            ->where($where)->get()->row();
        return $row;
    }
}