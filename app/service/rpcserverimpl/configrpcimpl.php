<?php

namespace App\service\rpcserverimpl;

//use Storage\Storage;
//use Lib\UserData;
use App\service\exception\AllErrorException;

class ConfigRpcImpl extends BaseRpcImpl
{

    /**
     *
     * @JsonRpcMethod
     */
    public function config($params)
    {
        //验证
        if (empty($params->key)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }
        $configModel = new \Model\GloabConfig();
        $dbData = $configModel->getConfigByKey($params->key);
        if(!$dbData){
            throw new AllErrorException(AllErrorException::GET_CONF_EMPTY);
        }
        foreach ($dbData as &$value) {
            # code...
            $value['value'] = json_decode($value['value'],TRUE);
        }
        unset($value);
        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $dbData,
        );
    }

    /**
     * 新手福利，用户中心请求过来 充值定期获取具体激活哪一种红包
     * @JsonRpcMethod
     */
    public function getNewBirdActivityRules($params){
        if (empty($params->userId) || empty($params->days) ||empty($params->amount) ) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS);
        }

        //投资定期360天产品 40
        //投资定期90天产品 30
        //投资定期30天产品，投资金额≧4000元 20
        //投资定期30天产品，投资金额≧2000元 10
        switch ($params->days) {
                case 360:
                    $redpacket = 'register_redpacket_forty';
                    break;
                case 90:
                    $redpacket = 'register_redpacket_thirty';
                    break;
                case 30:
                    if($rechargeAmount >= 4000)
                        $redpacket = 'register_redpacket_forty';
                    elseif($rechargeAmount >= 2000)
                        $redpacket = 'register_redpacket_ten';
                    break;

                default:
                    # code...
                    break;
            }
        $awardRedModel = new \Model\AwardRedpacket();
        $awardInfo = $awardRedModel->getRedpacketInfoByName($redpacket);

        // var_dump($awardInfo);exit;
        $dbData['redAmount'] = $awardInfo['amount'];
        $dbData['sourceId'] = $awardInfo['id'];

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $dbData,
        );

    }

}
