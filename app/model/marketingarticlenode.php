<?php
namespace Model;
class MarketingArticleNode extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_article_node');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}