<?php
namespace Model;
class AwardNode extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('award_node');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}