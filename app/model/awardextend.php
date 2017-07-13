<?php
namespace Model;
use App\service\rpcserverimpl\SendCouponRpcImpl;
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
    public function send($type, $userId, $nodeId)
    {
        $mapType = [1=>2,2=>1,3=>3];

        $send = new SendCouponRpcImpl();
        return $send->sendAction($mapType[$type], $userId, $nodeId);
    }



    //处理奖品发放记录并返回成功数
    public function dealRecord($recordId)
    {
        $record = $this->where(['id'=>$recordId])->get()->rowArr();

        $userIds = $this->filterAndMapPhoneToUserIds($record['user']);
        $failNum = 0;
        $awardHandModel = new AwardHandRecord();
        foreach ($userIds as $userId) {

            $res =  $this->send($record['award_type'], $userId, $record['award_id']);
            $data = [];
            $data['user_id'] = $userId;
            $data['award_extend_id'] = $recordId;
            $data['status'] = 1;
            $data['ctime'] = date("Y-m-d H:i:s");
            $data['award_type'] = $record['award_type'];
            $data['award_id'] = $record['award_id'];
            $data['mark'] = '';
            if (!$res['is_ok']){
                $data['status'] = 0;
                $data['mark'] = $res['msg'];
                $failNum++;
            }

            $awardHandModel->add($data);

        }

        return $this->updateSendStatus($recordId, count($userIds)-$failNum
        );

    }

    private function filterAndMapPhoneToUserIds($user)
    {
        if (is_string($user)) {
            $user = explode(',', $user);
        }

        return array_unique(array_values($user));
    }


}