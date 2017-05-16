<?php
namespace Model;

class MarketingRevenueSharing extends Model
{

    public function __construct($pkVal = '')
    {
        parent::__construct('marketing_revenuesharing');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    //获取用户所有加息券
    public function getListByUserid($userId)
    {
        return $this->fields('id, source_name, rate, effective_start, effective_end, continuous_days, is_use')
            ->orderby("rate desc, effective_end asc")
            ->where("`user_id` = {$userId}")
            ->get()->resultArr();
    }

    //获取加息券详情
    public function info($couponId)
    {
        return $this->where("`id` = {$couponId}")->get()->rowArr();
    }

    //给用户添加记录
    public function addRevenueSharing($awardInfo, $type)
    {
        if (empty($awardInfo['amount']) || empty($type) || empty($awardInfo['user_id'])) {
            return false;
        }
        $promoterModel = new \Model\PromoterList();
        $promoter = $promoterModel ->getToBePromoter($awardInfo['from_user_id']);
        
        $data = array(
            'type'                  => $type,
            'user_id'               => $awardInfo['user_id'],
            'from_user_id'          => $awardInfo['from_user_id'],
            'cash_total'            => $awardInfo['cash_total'],
            'interest_coupon_total' => $awardInfo['interest_coupon_total'],
            'amount'                => $awardInfo['amount'],
            'start_time'            => $awardInfo['start_time'],
            'end_time'              => $awardInfo['end_time'],
            'status'                => '100',
            'create_time'           => date('Y-m-d H:i:s'),
            'update_time'           => date('Y-m-d H:i:s'),
            'rate'                  => $promoter?200:100
        );

        $result = $this->fields('id')
            ->orderby("id desc")
            ->where("`user_id` = '{$awardInfo["user_id"]}' and `start_time`='{$awardInfo["start_time"]}' and `end_time`= '{$awardInfo["end_time"]}'")
            ->get()->rowArr();

        if ($result) {
            $where = ['id' => $result['id']];
            $this->where($where)->upd(['amount' => $awardInfo['amount']]);
        } else {
            $res = $this->add($data);
            if ($res) {
                $data['id'] = $res;
                return $data;
            }
        }

        return false;
    }

    /**
     * 计算完成
     * @param $userId
     * @return string
     */
    public function getSumByUserId($userId)
    {
        $result = $this->fields("SUM(amount) as amounts", false)
            ->where("`user_id` = '{$userId}'")
            ->whereIn("status", "200")
            ->get()
            ->rowArr();

        return empty($result['amounts']) ? '0.00' : $result['amounts'];
    }


    public function getSumByFromUserId($userId)
    {
        $result = $this->fields("SUM(amount) as amount", false)
            ->where("`from_user_id` = '{$userId}'")
            ->get()
            ->rowArr();

        return empty($result['amount']) ? '0.00' : $result['amount'];
    }

    public function getSumByUserIds($userIds)
    {
        $where = ['status' => '200'];
        $result = $this->fields("SUM(amount) as amount", false)
            ->where($where)
            ->whereIn('user_id', $userIds)
            ->get()
            ->rowArr();

        return empty($result['amount']) ? '0.00' : $result['amount'];
    }


    public function getYesterdayData()
    {
        $status = '100';
        $start_today = date("Y-m-d 00:00:00", strtotime("-1 day"));
        $end_today = date("Y-m-d 23:59:59", strtotime("-1 day"));

        $result = $this->fields("id, user_id, from_user_id, amount, cash_total, interest_coupon_total, start_time,end_time,status,create_time,update_time,rate", false)
            ->where(" `start_time` >= '{$start_today}' and `end_time` <= '{$end_today}' ")
            ->whereIn('status', $status)
            ->get()
            ->resultArr();

        return $result;
    }

    /**
     * @param $id
     * @param $userId
     * @param $type 400/200
     */
    public function successExecute($id, $userId, $type)
    {
        $where = ['id' => $id, 'from_user_id' => $userId];
        $this->where($where)->upd(['status' => $type]);
    }

    public function getAccountCount($userId, $start_time, $end_time)
    {
        $status = '100,200';
        $result = $this->fields("user_id", false)
            ->where(" `create_time` > '{$start_time}' and `create_time` < '{$end_time}' and `from_user_id` = '{$userId}'")
            ->whereIn('status', $status)
            ->groupby('user_id')
            ->get()
            ->resultArr();

        return $result;
    }
}