<?php
namespace Model;
class GloabConnfig extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('gloab_connfig');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}