<?php

namespace Model;

class RedeemCode extends Model
{

    private $typeToTable = [
        1 => 'award_interestcoupon',
        2 => 'award_experience',
        3 => 'award_withdraw',

    ];

    public $typeArr = [1=>'加息券',2=>'体验金',3=>'提现券'];

    const RANDOM_STR = 'S62ksVov';
    const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;

    public function verifyCode($userId, $code)
    {

        $this->tableName = 'redeem_code';
        $redeemCode = $this->where(
            ['code' => $code, 'is_del'=>0])
            ->get()->rowArr();

        //不存在或已使用
        if (!$redeemCode){
            return ['msg'=>'兑换码不存在','is_ok'=>false];
        }

        if ($redeemCode['status']==1) {
            return ['msg'=>'兑换码已兑换','is_ok'=>false];
        }

        $redeemUserCount = $this->
            where(['user_id' => $userId, 'meta_id'=>$redeemCode['meta_id']])
            ->get()->resultArr();
        $redeemUserCount = $redeemUserCount ? count($redeemUserCount) : 0;

        //切换库
        $this->tableName = 'redeem_code_meta';
        $redeemMeta = $this->where(['id' => $redeemCode['meta_id'],'is_del'=>0])->get()->rowArr();

        $time = time();
        if ($time<strtotime($redeemMeta['start_time'])){
            return ['msg' => '活动兑换码未开始', 'is_ok' => false];
        }

        if ($time>strtotime($redeemMeta['end_time'])){
            return ['msg' => '活动兑换码已过期', 'is_ok' => false];
        }

        if ($redeemMeta['status']==0) {
            return ['msg'=>'活动兑换码已禁用','is_ok'=>false];
        }

        if ($redeemUserCount >= $redeemMeta['user_max_get']){
            return ['msg'=>'已超过最高可得次数','is_ok'=>false];
        }

        if (!$this->verifyAble($redeemMeta['type'],$redeemMeta['map_id'])){
            return ['msg'=>'活动兑换码已禁用','is_ok'=>false];
        }


        $prizeInfo = $this->getPrizeInfo($redeemMeta['map_id'],$redeemMeta['type']);

        $pinfo = '';

        if ($redeemMeta['type']==1){
            $pinfo = $prizeInfo['rate'];
        }elseif ($redeemMeta['type']==2){
            $pinfo = $prizeInfo['amount'];
        }elseif ($redeemMeta['type']==3){
            $pinfo = $prizeInfo['times'];
        }

        $redeemCode['prize_info'] = $pinfo;
        return ['msg' => '', 'is_ok' => true, 'redeem_data'=>$redeemCode];



    }
    public function verifyAble($type, $mapId)
    {
        $this->tableName = $this->typeToTable[$type];

        $res = $this->where("`id` = '{$mapId}' and status = 1 and is_del = 0")
            ->get()->rowArr();
        if (empty($res)) return false;
        return true;

    }

    /**
     * 获取奖品详情
     * @param $id
     * @param $type
     * @return mixed
     */
    public function getPrizeInfo($id, $type)
    {
        $this->tableName = $this->typeToTable[$type];

        return $this->where("`id` = {$id} and status = 1 and is_del = 0")
            ->get()->rowArr();

    }

    public function getMetaList()
    {
        $this->tableName = 'redeem_code_meta';
        $res = $this->where('is_del =0')->get()->resultArr();
        if (!$res) return false;
        foreach ($res as $k=>$v){
            $res[$k]['type'] = $this->typeArr[$v['type']];
            $res[$k]['total'] = $this->getTotal($v['id']);
            $res[$k]['used'] = $this->getUseNum($v['id']);
            $res[$k]['un_used'] = $res[$k]['total'] - $res[$k]['used'];
        }

        return $res;
    }
    public function switchStausById($id)
    {
        $this->tableName = 'redeem_code_meta';
        $where = ['id' => $id];
        $row = $this->where($where)->get()->row();
        if ($row->status == self::STATUS_TRUE)
            $status = self::STATUS_FALSE;
        else
            $status = self::STATUS_TRUE;

        return $this->where($where)->upd(['status' => $status]);
    }


    /**
     * 兑换卷详情列表
     * @param $id 元信息表id
     * @param $start
     * @param $offset
     * @return mixed
     */
    public function getListRedeemDtail($id, $start=0, $offset=20)
    {
        $isUsed = [0 => '未使用', 1 => '已使用'];
        $this->tableName = 'redeem_code';
        $res =  $this->where(['meta_id' => $id, 'is_del' => 0])
            ->limit($start,$offset)
            ->get()
            ->resultArr();

        foreach ($res as $k=>$v){
            $res[$k]['type'] = $this->typeArr[$v['type']];
            $res[$k]['status'] = $isUsed[$v['status']];
            $res[$k]['desc'] = $this->getPrizeDetail($v['type'], $v['map_id']);
        }

        return $res;
    }

    public function getSearch($content)
    {
        $isUsed = [0 => '未使用', 1 => '已使用'];
        $this->tableName = 'redeem_code';
        $list =  $this->where(['is_del'=>0,  'code'=>$content])
                    ->get()
                    ->resultArr();

        $type = $list[0]['type'];
        $list[0]['type'] = $this->typeArr[$type];
        $list[0]['status'] = $isUsed[$list[0]['status']];

        $list[0]['desc'] = $this->getPrizeDetail($type, $list[0]['map_id']);

        $res['list'] = $list;
        $res['metaId'] = $list[0]['meta_id'];
        $res['type'] = $list[0]['type'];
        $res['total'] = $this->getTotal($list[0]['meta_id']);
        $res['used'] = $this->getUseNum($list[0]['meta_id']);

        return $res;
    }

    /**
     * @param $type
     * @param $prizeId
     * @return string
     */
    public function getPrizeDetail($type, $prizeId)
    {
        $this->tableName = $this->typeToTable[$type];
        $res = $this->where(['id' => $prizeId])->get()->resultArr();

        if ($type==1){
            $resStr =  "额度" . $res[0]['rate'] . "% , 时长" . $res[0]['days'] . "天";
        }elseif ($type==2){
            $resStr = "额度" . $res[0]['amount'] . "时长" . $res[0]['days']."天";
        }elseif ($type==3){
            $resStr = "提现次数" . $res[0]['times'] ."次";
        }

        return $resStr;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getUseNum($id)
    {
        $sql = "select count(*) as num 
                from redeem_code 
                where meta_id=$id and user_id>0 and is_del=0";
        $res = $this->query($sql)->resultArr();
        return $res[0]['num'];
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getTotal($id)
    {
        $sql = "select count(*) as num 
                from redeem_code 
                where meta_id=$id and is_del=0";
        $res = $this->query($sql)->resultArr();
        return $res[0]['num'];
    }


    /**
     * @param $data
     * @return mixed
     */
    public function addRedeem($data)
    {
        $this->tableName = 'redeem_code_meta';
        return parent::add($data);
    }

    /**
     * @param $meatId
     * @param $data
     * @return int
     */
    public function generateCode($meatId, $data)
    {
        $this->tableName = 'redeem_code';
        $redeemSn = $meatId."-".$data['type']."-";
        $count = 0;//计数器
        for ($i=0;$i<$data['total'];$i++){
            $subData['redeem_sn'] = $redeemSn.$i;
            $subData['code'] = self::hashEncode($redeemSn . $i);
            $subData['type'] = $data['type'];
            $subData['map_id'] = $data['map_id'];
            $subData['meta_id'] = $meatId;
            if (parent::add($subData)){
                $count++;
            }
        }
        return $count;
    }


    /**
     * 更新兑换码
     * @param $code
     * @param $userId
     * @return mixed
     */
    public function updateStatus($code, $userId)
    {
        $this->tableName = 'redeem_code';
        return $this->update(['user_id' => $userId,'status'=>1,
            'redeem_time' => date("Y-m-d H:i:s")], ['code' => $code]);
    }


    /**
     * @param $str
     * @return string
     */
    private static function hashEncode($str){
        $str1 = sprintf("%u",crc32(crypt($str,self::RANDOM_STR)));

        return base_convert($str1, 10, 32);
    }

}