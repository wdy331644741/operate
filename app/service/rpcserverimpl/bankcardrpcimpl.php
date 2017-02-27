<?php


namespace App\service\rpcserverimpl;

use App\service\Traits\Validator;
use App\service\exception\AllErrorException;
use Lib\UserData;

class BankcardRpcImpl extends BaseRpcImpl
{

    use Validator;

    /**
     * 绑卡状态
     */
    const BINDCARD_NOTBIND = 0;
    const BINDCARD_SUCCESS = 1;
    const BINDCARD_RELIEVE = 2;

    //绑卡成功标识
    const HANDLE_BINDCARD = "handle_bindcard";

    //银行卡列表类型
    const PC = 1;
    const APP = 2;

    //解绑
    const RELIEVE_SUCCESS = 1;
    const RELIEVE_FAILED = 0;

    /**
     * 获取银行卡信息
     *
     * @JsonRpcMethod
     */
    public function cardInfo($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //接口必要参数
        if (empty($params->cardno)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '缺少银行卡号');
        }

        //读取银行卡详细信息接口
        $result = $this->getCardInfo($this->userId, $params->cardno);

        //过滤部分数据
        unset($result['merchantaccount']);
        $result['bottom_quota'] = BOTTOM_QUOTA;
        $result['min_quota'] = MIN_WITHDRAD_QUOTA; //最低提现额度
        $result['max_quota'] = MAX_WITHDRAD_QUOTA; //最高提现额度

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $result
        );
    }

    /**
     * 银行卡绑定(第一步，绑卡信息)
     *
     * @JsonRpcMethod
     */
    public function bindBankCard($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //接口必要参数
        if (!isset($params->name) || !isset($params->id_number)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], "真实姓名或身份证号不为空");
        }

        //接口必要参数
        if (empty($params->cardno) || empty($params->phone)) {
            throw new AllErrorException(
                AllErrorException::API_MIS_PARAMS, [], '手机号或银行卡号不为空'
            );
        }

        if (empty($params->bankcode) || empty($params->bankname)) {
            throw new AllErrorException(
                AllErrorException::API_MIS_PARAMS, [], '请选择银行类型'
            );
        }

        //验证码手机号格式和身份证号格式
        if (!$this->validateIDnumber($params->id_number)) {
            throw new AllErrorException(AllErrorException::INPUT_IDCARD_LACK);
        }

        if (!$this->validatePhone($params->phone)) {
            throw new AllErrorException(AllErrorException::VALID_PHONE_FAIL);
        }

        //实例化模型
        $authIdentify = new \Model\AuthIdentify();
        $bankCardModel = new \Model\AuthBankCard();
        $bindReqModel = new \Model\AuthBankBindRequest();

        //身份证唯一判断
        $identifyResult = $authIdentify->getIdCardInfoByIdNumber($params->id_number);
        if (!empty($identifyResult) && $identifyResult['name'] == $params->name) {
            throw new AllErrorException(AllErrorException::REPEAT_IDENTIFY);
        }

        //接口调用银行卡信息
        $cardInfo = $this->getCardInfo($this->userId, $params->cardno);

        //检查银行渠道是否已绑定
//        if ($cardInfo['isbind'] == 1) {
//            throw new AllErrorException(AllErrorException::HAD_BIND_BANKCARD);
//        }

        //判断是否已经有账户绑定改卡
        $hadBindedList = $bankCardModel->getBankListByCard($params->cardno);
        if (!empty($hadBindedList)) {
            foreach ($hadBindedList as $item) {
                if ($item['user_id'] != $this->userId) {
                    throw new AllErrorException(AllErrorException::OTHER_BIND_CARD);
                }
            }
        }

        //判断是否可以绑定过银行卡
        $bindList = $bankCardModel->getBindCardList($this->userId);
        if (!empty($bindList)) {
            throw new AllErrorException(
                AllErrorException::HAD_BIND_BANKCARD, [], '一个账户只可以绑定一张银行卡'
            );
        }

        $reqData['requestid'] = generate_orderid(PREFIX_REQUESTID);
        $reqData['user_id'] = $this->userId;
        $reqData['name'] = $params->name;
        $reqData['idcardno'] = $params->id_number;
        $reqData['bankcode'] = $params->bankcode;
        $reqData['bankname'] = $params->bankname;
        $reqData['channel'] = $cardInfo['channel_code'];
        $reqData['cardno'] = (string) $params->cardno;
        $reqData['phone'] = (string) $params->phone;
        $reqData['userip'] = get_client_ip();
        $reqData['create_time'] = date("Y-m-d H:i:s");

        if ($bindReqModel->add($reqData)) {

            //请求预绑卡接口数据(因为参数顺序，所以单独写一份赋值，汗。。)
            $apiData['requestid'] = $reqData['requestid'];
            $apiData['cardno'] = $reqData['cardno'];
            $apiData['idcardno'] = $reqData['idcardno'];
            $apiData['username'] = $reqData['name'];
            $apiData['phone'] = $reqData['phone'];
            $apiData['identityid'] = $reqData['user_id'];
            $apiData['userip'] = $reqData['userip'];
            $apiData['bankcode'] = $reqData['bankcode'];
            $apiData['bankname'] = $reqData['bankname'];

            //请求绑卡接口
            Common::jsonRpcApiCall($apiData, 'bindCard', config('RPC_API.pay'));

            //增加绑卡关联
            $bindCardRes = $bankCardModel->addCardByReqLog($reqData);
            //增加实名认证
            $identifyRes = $authIdentify->addIdentifyByReqLog($reqData);

            if ($bindCardRes && $identifyRes) {
                //修改用户绑卡状态
                UserData::set('is_bindcard', true);
                //发放绑卡体验金
                Common::messageBroadcast('addExperience', array(
                    'user_id'   => $this->userId,
                    'node_name' => 'bindcard',
                    'time'      => date("Y-m-d H:i:s")
                ));

                return array(
                    'code'    => 0,
                    'message' => 'success'
                );
            }
        }
        //mysql error
        throw new AllErrorException(AllErrorException::SAVE_BIND_CARD_FAIL);

    }

    /**
     * 确认绑卡操作
     *
     * @JsonRpcMethod
     */
    public function confirmBandCard($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //接口必要参数
        if (empty($params->requestid)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '请先获取短信验证码');
        }

        if (empty($params->validCode)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '短信验证码不为空');
        }

        //确认绑卡接口参数
        $data['requestid'] = generate_orderid(PREFIX_REQUESTID);
        $data['pre_requestid'] = (string) $params->requestid;
        $data['validatecode'] = (string) $params->validCode;
//        $data['amount'] = (string) $params->validCode;

        $authIdentify = new \Model\AuthIdentify();
        $bankCardModel = new \Model\AuthBankCard();
        $bindReqModel = new \Model\AuthBankBindRequest();

        //获取绑卡请求信息
        $reqLog = $bindReqModel->getReqLogByReqId($data['pre_requestid']);

        //非法，无效的绑卡请求
        if (empty($reqLog)) {
            throw new AllErrorException(AllErrorException::API_ILLEGAL, [], '请先获取短信验证码');
        }

        //调用确认绑卡接口
        $result = Common::jsonRpcApiCall($data, 'bindCardConfirm', config('RPC_API.pay'));

        //状态为1 成功
        if (
            isset($result['result']['status']) &&
            $result['result']['status'] == self::BINDCARD_SUCCESS
        ) {
            //增加绑卡关联
            $bindCardRes = $bankCardModel->addCardByReqLog($reqLog);
            //增加实名认证
            $identifyRes = $authIdentify->addIdentifyByReqLog($reqLog);

            if ($bindCardRes && $identifyRes) {
                //修改用户绑卡状态
                UserData::set('is_bindcard', true);

                return array(
                    'code'    => 0,
                    'message' => 'success'
                );
            }
        }

        throw new AllErrorException(AllErrorException::SAVE_BIND_CARD_FAIL);
    }

    /**
     * 获取用户绑卡信息
     *
     * @JsonRpcMethod
     */
    public function bindCardList()
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //检查是否绑卡
        $bankCardModel = new \Model\AuthBankCard();
        $bankCardList = $bankCardModel->getBindCardList($this->userId);

        if (!Common::bindCardChecked($this->userId)) {
            throw new AllErrorException(AllErrorException::NOT_BIND_BANKCARD);
        }

        //调取限额数据和是否发送短信
        foreach ($bankCardList as $key => $card) {
            //读取银行卡详细信息接口
            $cardInfo = $this->getCardInfo($this->userId, $card['cardno']);

            $bankCardList[ $key ]['channel'] = $cardInfo['channel_code'];
            $bankCardList[ $key ]['status'] = $cardInfo['isbind'];
            $bankCardList[ $key ]['bottom_quota'] = BOTTOM_QUOTA;
            $bankCardList[ $key ]['first_quota'] = I("data.first_quota", 0, 'floatval', $cardInfo);;
            $bankCardList[ $key ]['times_quota'] = I("data.times_quota", 0, 'floatval', $cardInfo);
            $bankCardList[ $key ]['days_quota'] = I("data.days_quota", 0, 'floatval', $cardInfo);
            $bankCardList[ $key ]['smsconfirm'] = I("data.smsconfirm", 0, 'intval', $cardInfo);
            $bankCardList[ $key ]['bank_logo'] = $cardInfo['bank_logo'];
            $bankCardList[ $key ]['min_quota'] = MIN_WITHDRAD_QUOTA; //最低提现额度
            $bankCardList[ $key ]['max_quota'] = MAX_WITHDRAD_QUOTA; //最高提现额度
        }

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $bankCardList
        );
    }

    /**
     * 解除银行卡绑定
     *
     * @JsonRpcMethod
     *
     * @return array
     * @throws AllErrorException
     */
    public function relieveBindBankCard()
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //检查是否绑卡
        if (!Common::bindCardChecked($this->userId)) {
            throw new AllErrorException(AllErrorException::NOT_BIND_BANKCARD);
        }

        //
        return array(
            'code'    => 0,
            'message' => 'success'
        );

        //mysql error
        throw new AllErrorException(AllErrorException::RELIEVE_BANKCARD_FAIL);
    }

    /**
     * 支持银行列表
     *
     * @JsonRpcMethod
     */
    public function supportBankList($params)
    {
        $type = isset($params->type) ? $params->type : self::APP;

        $data = array();
        $params = array(
            'page'      => 1,        //当期页数
            "page_size" => 100, //每页记录数
            "type"      => $type     //列表类型 1为PC  2为APP
        );

        $result = Common::jsonRpcApiCall($params, 'lists', config('RPC_API.bank'));

        foreach ($result['result']['list'] as $value) {
            $value['bottom_quota'] = BOTTOM_QUOTA; //最低充值金额
            $data[] = $value;
        }

        return array(
            'code'    => 0,
            'message' => 'success',
            'data'    => $data
        );
    }

    /**
     * 绑卡并充值
     *
     * @JsonRpcMethod
     *
     * @param $params
     *
     * @return array
     * @throws AllErrorException
     */
    public function rechargeAndBindcard($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }
        //接口必要参数
        if (empty($params->requestid) || empty($params->order_id)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '请先获取短信验证码');
        }

        if (empty($params->validCode)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '短信验证码不为空');
        }

        //接口必要参数
        if (empty($params->trade_pwd)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '请输入交易密码');
        }

        $rechargeModel = new \Model\MarginRecharge();
        //查询充值记录
        $rechargeInfo = $rechargeModel
            ->where("`order_id`='{$params->order_id}' AND `status` = 100")
            ->get()->rowArr();
        if (empty($rechargeInfo)) {
            throw new AllErrorException(
                AllErrorException::API_ILLEGAL, [], '订单无效，请重新获取验证码'
            );
        }

        //调用rpc接口，检查交易密码是否正确
        Common::localApiCall($params, 'checkTradePwd', 'SecureRpcImpl');

        //构造接口请求参数
        $apiData = new \stdClass();
        $apiData->orderid = (string) $params->order_id;
        $apiData->requestid = generate_orderid();
        $apiData->pre_requestid = (string) $params->requestid;
        $apiData->validatecode = (string) $params->validCode;

        //调用绑卡并充值接口
        $result = Common::jsonRpcApiCall($apiData, 'bindAndPayConfirm', config('RPC_API.pay'), false);

        //处理订单及绑卡状态，并返回数据信息
        return OrderStatus::getRechargeStatus($result, $this->userId, $params->order_id, false, $params->requestid);

    }

    /**
     * 同卡
     * @JsonRpcMethod
     */
    public function retainSingleBankCard($params)
    {
        //检查登录状态
        if (($this->userId = $this->checkLoginStatus()) === false) {
            throw new AllErrorException(AllErrorException::VALID_TOKEN_FAIL);
        }

        //接口必要参数
        if (empty($params->cardno)) {
            throw new AllErrorException(
                AllErrorException::API_MIS_PARAMS, [], '请选择要保留的银行卡'
            );
        }

        $bankCardModel = new \Model\AuthBankCard();
        $bindList = $bankCardModel->getAllBindCardList($this->userId);

        //所选银行卡检查
        if (
            empty($bindList) ||
            !in_array($params->cardno, array_column($bindList, 'cardno'))
        ) {
            throw new AllErrorException(
                AllErrorException::API_ILLEGAL, [], '非法请求，用户未绑定所选银行卡'
            );
        }

        $selectedNo = $params->cardno;

        //过滤绑定银行卡列表，保留所选银行卡
        $relieveList = array_filter($bindList, function ($cardInfo) use ($selectedNo) {
            return $cardInfo['cardno'] != $selectedNo;
        });

        //解绑
        foreach ($relieveList as $cardInfo) {
            $result = $this->relieveBankCard($this->userId, $cardInfo);
            $status = ($result == self::RELIEVE_SUCCESS) ? self::BINDCARD_RELIEVE : self::BINDCARD_NOTBIND;

            //更新银行卡绑定状态
            $res = $bankCardModel->update(
                array('status' => $status, 'is_tk' => 1, 'update_time' => date("Y-m-d H:i:s")),
                array('id' => $cardInfo['id'])
            );

            if (!$res) {
                logs('res:' . $res . PHP_EOL . $bankCardModel->getLastQuery(), 'relieve');
                throw new AllErrorException(AllErrorException::RELIEVE_BANKCARD_FAIL);
            }
        }

        //同卡完成
        return array(
            'code'    => 0,
            'message' => 'success'
        );
    }

    //解绑卡接口调用
    protected function relieveBankCard($userId, $cardInfo)
    {
        $solutionCardModel = new \Model\SolutionCardRequest();
        //接口请求参数
        $params = array(
            'requestid'    => generate_orderid(),
            'cardno'       => $cardInfo['cardno'],
            'identityid'   => $userId,
            'channel_code' => $cardInfo['channel']
        );

        //解绑卡接口
        $response = Common::jsonRpcApiCall(
            (object) $params,
            'bankCardUnbind',
            config('RPC_API.pay'),
            false
        );

        if (isset($response['error'])) {
            $status = self::RELIEVE_FAILED;
            $resMsg = $cardInfo['channel'] . '--' . $response['error']['message'];

            //请求日志记录
            Common::debugTrace($params, 'bankCardUnbind', $response);

        } else {
            $status = self::RELIEVE_SUCCESS;
            $resMsg = $cardInfo['channel'] . '  ' . $response['result']['resp_msg'];
        }

        $solutionCardModel->add(array(
            'bid'       => $cardInfo['id'],
            'requestid' => $params['requestid'],
            'name'      => $cardInfo['realname'],
            'status'    => $status,
            'resp_msg'  => $resMsg
        ));

        return $status;
    }
}
