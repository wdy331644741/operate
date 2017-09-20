<?php
namespace Model;
class MarketingIndex extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_index');
        if ($pkVal)
            $this->initArData($pkVal);
    }


    public function getDefaultSlogen(){
    	return $this->where(['status'=>1,'pos'=>1,'is_del'=>0])->get()->rowArr();

    }

    public function getMomentSlogen(){
    	return true;
    }
}