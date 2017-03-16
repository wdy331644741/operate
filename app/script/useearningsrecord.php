<?php
    defined("__FRAMEWORKNAME__") or die("No permission to access!");
    /**
     * 发放脚本
     * @pageroute
     */

    const getNum = 100;
    function index()
    {
        $time = date('Y-m-d');
        $earningsRecordModel = new \Model\EarningsRecord();
        $useCashRecordModel= new \Model\UseCashRecord();
        $countAll = $earningsRecordModel->getEarningsRecordCountByTime($time);
        $loopNum = bcdiv($countAll['total'], getNum);
        $loopNum = bcadd($loopNum,1);
        for($i=1;$i<=$loopNum;$i++){
            $start = ($i - 1) * getNum;
            $earningsRecordList=$earningsRecordModel->getEarningsRecordCalculationByLimit($time, $start, getNum);
            foreach ($earningsRecordList as $k =>$v){
                //给奇哥组装的数组
                $params=[
                    'type'=>'活期收益',
                    'token'=>$v['token'],
                    'amount'=>$v['amount'],
                    'userId'=>$v['user_id'],
                    'refundId'=>$v['id'],
                    'expInterest'=>$v['experience_amount'],
                    'increase'=>$v['interest_coupon'],
                    'interest'=>$v['basics_amount'],
                ];

                $userServerLib= new \Lib\UserServer();
                $result=$userServerLib->grantEarningsRecord($params);
                if(isset($result['error']) && $result['error']['code']==-1 && stripos($result['error']['message'],'curl')!==false  ){
                    $result=$userServerLib->grantEarningsRecord($params);
                }

                logs($result,'updateEarningsRecord');

                if(isset($result['result']) && $result['result'] !==false){
                    echo "ok";
                    $recordId=$useCashRecordModel->addProfitCash($v);
                    echo $recordId."成功\r\n";
                    logs($recordId,'recordId');
                }

            }
        }
    }
