<?php
namespace Model;
class AwardRedpacket extends Model
{
	const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;
    const TYPE_NORMAL = 0;
    const TYPE_RAND = 1;
    public function __construct($pkVal = '')
    {
        parent::__construct('award_redpacket');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function switchStausById($id)
    {
        $where = ['id' => $id];
        $row = $this->where($where)->get()->row();
        if ($row->status == self::STATUS_TRUE)
            $status = self::STATUS_FALSE;
        else
            $status = self::STATUS_TRUE;

        return $this->where($where)->upd(['status' => $status]);
    }
}