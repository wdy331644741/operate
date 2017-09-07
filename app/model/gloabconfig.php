<?php
namespace Model;
class GloabConfig extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('gloab_config');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}