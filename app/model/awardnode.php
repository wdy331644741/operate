<?php
namespace Model;

class AwardNode extends Model {

    public function __construct($pkVal = '')
    {
        parent::__construct('award_node');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function getNode($nodeName)
    {
        if (empty($nodeName)) {
            return false;
        }

        $node = $this->where("`name` = '{$nodeName}'")->get()->rowArr();

        return $node['id'];
    }
}