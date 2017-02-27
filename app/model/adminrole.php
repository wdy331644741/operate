<?php
namespace Model;
class AdminRole extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('admin_role');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}