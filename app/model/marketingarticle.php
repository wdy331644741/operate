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
    public function rowcounts(){
        $articleNode = new MarketingArticleNode();
        $noticeCate = $articleNode->where("`name` = 'notice'")->get()->rowArr();

        return $this->fields('count(*)')
            ->where("`is_del` = 0 and `status` = 1 and cate_node = {$noticeCate['id']}")
            ->get()->row();
    }

    public function noticeList($page)
    {
        $start = intval(($page - 1) * 10);
        $articleNode = new MarketingArticleNode();
        $noticeCate = $articleNode->where("`name` = 'notice'")->get()->rowArr();

        return $this->fields('id, title, content,create_time')
            ->where("`is_del` = 0 and `status` = 1 and cate_node = {$noticeCate['id']}")
            ->orderby(array('sort'=>'DESC','create_time'=>'DESC'))
            ->limit($start, 10)
            ->get()->resultArr();
    }

    public function getActicle($id){

        return $this->fields('content')
            ->where("`is_del` = 0 and `status` = 1 and id = {$id}")
            ->get()->resultArr();
    }

    //返回用户是否有未读公告
    //0没有   1有未读公告
    public function haveUnreadActicle($userId){
        $articleNode = new MarketingArticleNode();
        $noticeCate = $articleNode->where("`name` = 'notice'")->get()->rowArr();
        $sql = "select id from marketing_article where id not in (select article_id from marketing_article_log where user_id = {$userId}) and is_del = 0 and  status = 1 and cate_node = {$noticeCate['id']}";
        $res = $this->query($sql)->resultArr();
        return empty($res)?0:1;
    }
}