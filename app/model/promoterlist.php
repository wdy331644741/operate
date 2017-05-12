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
}