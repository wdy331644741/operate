<?php
namespace Model;
class PromoterStatistics extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('promoter_statistics');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}