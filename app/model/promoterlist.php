<?php
namespace Model;
class PromoterList extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('promoter_list');
        if ($pkVal)
            $this->initArData($pkVal);
    }
    /**
     * 返回所有审核通过的推广员
     * @return [type] [description]
     */
    public function allPromotersInfo(){
        return $this->fields('auth_id,invite_num,level,earnings')
            ->where("`status` = 1")
            ->get()->resultArr();
    }
    /**
     * 判断用户 是否有过待审核记录或是已通过
     * @return [type] [description]
     */
    public function getIsExistByUser($auth_id){
        $promoterInfo = $this->fields('status,create_time')
            // ->where(array('auth_id'=>$auth_id,'status'))
            ->where("`auth_id` = '{$auth_id}' AND `status` IN (0,1) ")
            ->orderby("create_time DESC")
            ->get()->resultArr();
        return $promoterInfo;
    }

    /**
     * 判断是否成为推广员
     * @return [type] [description]
     */
    public function getToBePromoter($auth_id){
        $promoterInfo = $this->fields('status,create_time')
            ->where("`auth_id` = '{$auth_id}' AND `status` = 1")
            ->get()->resultArr();

        return empty($promoterInfo)?false:true;
    }


    /**
     * 根据用户查询用户是否存在
     * @param $auth_id
     * @return status
     */
    public function getPromoterInfoById($auth_id)
    {
        $promoterInfo = $this->fields('status,create_time')
            ->where(array('auth_id'=>$auth_id))
            ->orderby("create_time DESC")
            ->get()->resultArr();
        return $promoterInfo;
        // if($promoterInfo){
        //     return $promoterInfo;
        // }else{
        //     return false;
        // }

    }

    /**
     * 增加推广员
     * @param array $data [推广员数据]
     * @return bool
     */
    public function addPromoter($data = array()){

        // if(!empty($data)){
        //     $this->auth_id = $data['auth_id'];
        //     $this->invite_num = $data['invite_num'];
        //     $this->total_inve_amount = $data['total_inve_amount'];
        //     $this->commission = $data['commission'];
        // }
        // $data['username'] = '1';//从用户中心获取信息
        // $data['phone'] = '1';

        if (!empty($data) && $this->add($data)) {
            return ture;
        } else {
            return false;
        }

    }

    /**
     * 增加promoter 中好友投资总额
     * @param [type] $promoter_id [description]
     * @param [type] $recharge    [description]
     */
    public function addPromoterFriendRecharge($promoter_id,$recharge){
        $data = array(
            'total_inve_amount+' => $recharge
            );
        $where = array('auth_id' => $promoter_id);
        $sql = "update promoter_list set total_inve_amount = total_inve_amount+{$recharge} where auth_id = {$promoter_id}";
        $this->exec($sql);
    }

    /**
     * 增加推广员的邀请好友数量+1
     * @param  [type] $promoter_id [description]
     * @return [type]              [description]
     */
    public function upPromoterFriendCounts($promoter_id){
        $sql = "update promoter_list set invite_num = invite_num+1 where auth_id = {$promoter_id}";
        $this->exec($sql);
    }

    /**
     * 更新推广员-好友邀请所得佣金
     * @param  [type] $promoter_id [description]
     * @param  [type] $amount      [description]
     * @return [type]              [description]
     */
    public function upPromoterShareAmount($promoter_id,$amount){
        $sql = "update promoter_list set commission = commission+{$amount} where auth_id = {$promoter_id}";

        $this->exec($sql);
    }
}