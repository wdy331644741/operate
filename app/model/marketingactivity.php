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
        return $this->where($where)->upd(['status' => $status]);
    }

    public function activityList($page)
    {
        // $start = intval(($page - 1) * C('PAGE_SIZE'));
        $nowtime = date("Y-m-d H:i:s");
        return $this->fields('id, title, img_url, link_url, start_time, end_time, desc')
            ->where("`is_del` = 0 and `status` = 1 ")
            ->orderby("sort DESC")
            // ->limit($start, C('PAGE_SIZE'))
            ->get()->resultArr();
    }
}