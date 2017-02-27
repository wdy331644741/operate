<?php
namespace Model;
class AdminNode extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('admin_node');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}