<?php
namespace Model;
class MarketingArticleLog extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_article_log');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    //获取阅读记录
    public function isReadByUser($userId,$articleId = ''){
    	if(empty($articleId)){
    		return $this->fields('id,article_id,counts,create_time')
	    		->where("`user_id` = '{$userId}'")
	    		->get()
	    		->resultArr();
    	}

    	return $this->fields('id,counts,create_time')
    		->where("`user_id` = '{$userId}' AND `article_id` = '{$articleId}'")
    		->get()
    		->resultArr();

    }

    //插入一条日志记录
    public function addReadLog($articleId,$userId){
    	$date = date('Y-m-d H:i:s',time());
    	$data = array(
    			'user_id' => $userId, 
    			'article_id' => $articleId, 
    			'create_time' => $date
    		);
    	$result = $this->add($data);
    	return $result;
    }
    //更新 增加阅读次数
    public function updateReadLog($articleId,$userId,$count){
    	$date = date('Y-m-d H:i:s',time());
    	$update = array(
    			'last_time' => $date,
    			'counts' => $count+1,
    		);
    	$where = array(
    			'user_id' => $userId, 
    			'article_id' => $articleId, 
    		);
    	$result = $this->update($update,$where);
    	return $result;
    }	
}