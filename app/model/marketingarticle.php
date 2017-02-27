<?php
namespace Model;
class MarketingArticle extends Model
{
    const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;

    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_article');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function switchStausById($id)
    {
        $where = ['id' => $id];
        $row = $this->where($where)->get()->row();
        if ($row->status == self::STATUS_TRUE)
            $status =  self::STATUS_FALSE;
        else
            $status =  self::STATUS_TRUE;
        return $this->where($where)->upd(['status' => $status]);
    }

    public function noticeList()
    {
        $articleNode = new MarketingArticleNode();
        $noticeCate = $articleNode->where("`name` = 'notice'")->get()->rowArr();

        return $this->fields('id, title, content')
            ->where("`is_del` = 0 and `status` = 1 and cate_node = {$noticeCate['id']}")
            ->orderby("sort DESC")
            ->get()->resultArr();
    }

}