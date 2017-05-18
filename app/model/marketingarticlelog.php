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
}