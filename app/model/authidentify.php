<?php
namespace Model;

class AuthIdentify extends Model {

    static $area = array(
        11 => '北京',
        12 => '天津',
        13 => '河北',
        14 => '山西',
        15 => '内蒙古',
        21 => '辽宁',
        22 => '吉林',
        23 => '黑龙江',
        31 => '上海',
        32 => '江苏',
        33 => '浙江',
        34 => '安徽',
        35 => '福建',
        36 => '江西',
        37 => '山东',
        41 => '河南',
        42 => '湖北',
        43 => '湖南',
        44 => '广东',
        45 => '广西',
        46 => '海南',
        50 => '重庆',
        51 => '四川',
        52 => '贵州',
        53 => '云南',
        54 => '西藏',
        61 => '陕西',
        62 => '甘肃',
        63 => '青海',
        64 => '宁夏',
        65 => '新疆',
        71 => '台湾',
        81 => '香港',
        91 => '澳门',
    );

    public function __construct($pkVal = '')
    {
        parent::__construct('auth_identify');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    /**
     * 获取身份信息
     */
    public function getIdCardInfoByUserId($user_id)
    {
        return $this->where(array('user_id' => $user_id))->get()->rowArr();
    }

    /**
     * 获取身份信息(身份证号查询)
     */
    public function getIdCardInfoByIdNumber($IdNumber)
    {
        return $this->where("`id_number` = '{$IdNumber}' and `is_valid` = 1")->get()->rowArr();
    }

    /**
     * 获取身份证所在的省份
     */
    public function getProvinceByIdNumber($IdNumber)
    {
        $provinceCode = substr($IdNumber, 0, 2);

        return self::$area[ $provinceCode ];
    }

    /**
     * 实名认证检查
     */
    public function identifyChecked($user_id)
    {
        $IdCardInfo = $this->getIdCardInfoByUserId($user_id);
        if (empty($IdCardInfo) || $IdCardInfo['is_valid'] != 1) {
            return false;
        }

        return $IdCardInfo;
    }

    public function addIdentifyByReqLog($reqLog)
    {
        $identifyInfo = $this->getIdCardInfoByUserId($reqLog['user_id']);

        if (empty($identifyInfo)) {
            $identify['user_id'] = $reqLog['user_id'];
            $identify['name'] = $reqLog['name'];
            $identify['id_number'] = $reqLog['idcardno'];
            $identify['is_valid'] = 1;
            $identify['province'] = $this->getProvinceByIdNumber($reqLog['idcardno']);
            $identify['from_client'] = getUAInfo('platform');
            $identify['create_time'] = date("Y-m-d H:i:s");
            $res = $this->add($identify);
        } else {
            $res = $this->update(
                array(
                    'name'        => $reqLog['name'],
                    'id_number'   => $reqLog['idcardno'],
                    'is_valid'    => 1,
                    'province'    => $this->getProvinceByIdNumber($reqLog['idcardno']),
                    'from_client' => getUAInfo('platform'),
                    'update_time' => date("Y-m-d H:i:s")
                ),
                array('id' => $identifyInfo['id'])
            );
        }

        //更新用户表信息
        $authUserModel = new AuthUser($reqLog['user_id']);
        $authUserModel->realname = $reqLog['name'];
        $authUserModel->id_number = $reqLog['idcardno'];
        $authUserModel->gender = substr($reqLog['idcardno'], -2, 1) % 2 == 0 ? 2 : 1;
        $authUserModel->birthday = date("Y-m-d", strtotime(substr($reqLog['idcardno'], 6, 8)));
        $authUserModel->save();

        //缓存失效
        invalidUserProfileCache($reqLog['user_id']);

        return $res;
    }

}