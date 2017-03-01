<?php
namespace Model;
class MarketingBanner extends Model
{
    const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;

    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_banner');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function activedBanners()
    {
        $nowtime = date("Y-m-d H:i:s");
        return $this->fields('id, title, img_url, link_url')
            ->where("`is_del` = 0 and `status` = 1 and `start_time` <= '{$nowtime}' and `end_time` > '{$nowtime}'")
            ->orderby("sort DESC")
            ->get()->resultArr();
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