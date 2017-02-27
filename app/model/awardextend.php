<?php
namespace Model;
class AwardExtend extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('award_extend');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function getUnsendRecords()
    {
        return $this->where("`send_status` = '0'")->get()->resultArr();
    }

    public function updateSendStatus($id, $successNum = 0)
    {
        return $this->where("id = '{$id}'")
            ->upd(array(
                    'send_count' => $successNum,
                    'send_status' => 1,
                    'update_time' => date("Y-m-d H:i:s")
                ));
    }
}