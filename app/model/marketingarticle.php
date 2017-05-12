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
            $status = self::STATUS_FALSE;
        else
            $status = self::STATUS_TRUE;
        return $this->where($where)->upd(['status' => $status]);
    }

    public function noticeList($page)
    {
        $start = intval(($page - 1) * C('PAGE_SIZE'));
        $articleNode = new MarketingArticleNode();
        $noticeCate = $articleNode->where("`name` = 'notice'")->get()->rowArr();

        return $this->fields('id, title, content')
            ->where("`is_del` = 0 and `status` = 1 and cate_node = {$noticeCate['id']}")
            ->orderby(array('sort'=>'DESC','create_time'=>'DESC'))
            ->limit($start, C('PAGE_SIZE'))
            ->get()->resultArr();
    }

    public function getActicle($id){

        return $this->fields('content')
            ->where("`is_del` = 0 and `status` = 1 and id = {$id}")
            ->get()->resultArr();
    }
}