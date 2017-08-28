<?php
namespace App\service\exception;

use Lib\JsonRpcBasicErrorException as BasicException;

class AllErrorException extends BasicException
{
    /**
     * 错误分类
     * 10xx 接口调用错误
     * 11xx 参数验证错误
     * 12xx 输入错误
     * 13xx 逻辑错误
     * 15xx 服务器错误
     */

    //接口调用错误
    const API_MIS_PARAMS = 1000;
    const API_ILLEGAL = 1001;
    const API_BUSY = 1002;
    const API_FAILED = 1003;
    const API_EXPIRE = 1004;
    const API_SMS = 1005;
    const API_ERROR_PARAMS = 1006;

    //参数验证错误
    const VALID_CAPTCHA_FAIL = 1100;
    const VALID_SMS_FAIL = 1101;
    const VALID_PWD_FAIL = 1102;
    const VALID_PHONE_FAIL = 1103;
    const VALID_PHONE_USED = 1104;
    const VALID_BANKCARD_FAIL = 1105;
    const VALID_TOKEN_FAIL = 1106;
    const VALID_NUMERICAL_FAIL = 1107;
    const VALID_FEEDBACK_FAIL = 1108;
    const VALID_ATTENTIONS_FAIL = 1109;
    const VALID_WITHDRAW_FAIL = 1110;
    const VALID_POR_FAIL = 1111; // 佣金奖励
    const VALID_AGE_FAIL = 1112; //未满18

    //输入错误
    const INPUT_CAPTCHA_MIS = 1200;
    const INPUT_SMSCODE_MIS = 1201;
    const INPUT_VERIFY_FAIL = 1202;
    const INPUT_PWD_ERROR = 1203;
    const INPUT_OLDPWD_ERROR = 1204;
    const INPUT_INCONSISTENT = 1205;
    const INPUT_USERNAME_LACK = 1206;
    const INPUT_ADDRESS_LACK = 1207;
    const INPUT_PHONE_LACK = 1208;
    const INPUT_IDCARD_LACK = 1209;
    const INPUT_IDCARD_ERROR = 1210;
    const INPUT_REQ_ID_MIS = 1211;
    const INPUT_CARD_ERROR = 1212;
    const CARD_NOT_SUPPORT = 1213;
    const BINDCARD_FAIL = 1214;
    const NOT_CHECKED_PROTOCOL = 1215;


    //逻辑错误
    const NOT_SET_TRADE_PWD = 1300;
    const NOT_IDENTIFY = 1301;
    const HAD_IDENTIFIED = 1302;
    const NOT_BIND_BANKCARD = 1303;
    const HAD_BIND_BANKCARD = 1304;
    const HAD_SET_TRADE_PWD = 1305;
    const USERNAME_NOT_EXIST = 1306;
    const BIND_BANKCARD_AGAIN = 1307;
    const REPEAT_IDENTIFY = 1308;
    const OTHER_BIND_CARD = 1309;
    const HAD_CHECKED_IN = 1310;


    //服务器错误
    const SAVE_USER_FAIL = 1400;
    const SAVE_ADDRESS_FAIL = 1401;
    const SAVE_TRADE_PWD_FAIL = 1402;
    const SAVE_BIND_CARD_FAIL = 1403;
    const MODIFY_BIND_PHONE_FAIL = 1404;
    const SAVE_IDENTIFY_FAIL = 1405;
    const SAVE_CHARGE_FAIL = 1406;
    const RELIEVE_BANKCARD_FAIL = 1407;
    const SAVE_SIGNIN_PWD_FAIL = 1408;
    const SAVE_FEEDBACK_FAIL = 1409;
    const SAVE_WITHDRAE_FAIL = 1410;
    const SAVE_NOTIFICATION_FAIL = 1411;
    const SAVE_BANKCHANNEL_FAIL = 1412;
    const SAVE_CHECKIN_FAIL = 1413;

    //第三方合作账号相关错误
    const ACCOUNT_BIND_FAIL = 1440;
    const ACCOUNT_UNBIND_FAIL = 1441;
    const ACCOUNT_NOTBIND = 1442;
    const ACCOUNT_ALREADY_BIND = 1443;


    //账号异常
    const ACCOUNT_TRADE_FROZEN = 1500;
    const ACCOUNT_SIGNIN_FROZEN = 1501;
    const ACCOUNT_IDENTIFY_FROZEN = 1502;

    const REMOTE_SIGNIN = 1510;

    //服务器异常
    const SERVER_ERROR = 1999;

    ####交易类####

    //散标月利宝
    const CREATE_ORDER_FAIL = 1600; //创建交易订单失败
    const OPEN_ORDER_FAIL = 1601; //打开交易订单失败
    const UPDATE_ORDER_FAIL = 1602; //更新交易订单失败
    const UPDATE_USER_MARGIN_FAIL = 1603; //更新用户资产失败
    const WRITE_MARGINRECORD_FAIL = 1604; //记资产流水失败



    //还款
    const CREATE_REFUNDORDER_FAIL = 1620; //创建还款订单失败
    const REFUND_INTEREST_FAIL = 1621; //利息入账失败
    const REFUND_INCREASE_FAIL = 1622; //加息入账失败
    const REFUND_EXPINTEREST_FAIL = 1623; //体验金利息入账失败
    const REFUND_FAIL = 1625; //还款失败

    //体验金
    const INVEST_EXPERIENCE_FAIL = 1630; //投资体验金失败
    const UPDATE_EXPERIENCE_FAIL = 1631; //更新体验金使用状态失败
    const EXPERIENCE_AMOUNT_ILLEGAL = 1632; //体验金数额不正确
    const EXPERIENCE_IDS_ISUSED = 1633; //此体验金已经用过了

    //红包
    const REDPACKET_USE_FAIL = 1640; //使用红包失败
    const REDPACKET_RETREAT_FAIL = 1641; //退回红包失败

    //加息券
    const INTERESTCOUPON_USE_FAIL = 1642; //加息使用失败
    const INTERESTCOUPON_RETREAT_FAIL = 1643; //加息退回失败

    //流标退款
    const FAIL_PRODUCT_REFUND_FAIL = 1650; //流标退款失败

    //满标审核
    const UNFROZEN_FAIL = 1660; //满标审核失败

    //充值、提现
    const RECHARGE_FAIL = 1670; //充值失败
    const RECHARGE_DEALING = 1671; //充值处理中
    const RECHARGE_CANCEL = 1672; //充值已取消
    const BINDCARD_NOT_RECHARGE = 1673; //绑卡成功，充值失败


    //运营 活动
    const COUPON_UNDIFIND = 7001; //相关劵信息不存在


    //运营
    const ACTIVATE_NODE = 7111; //获取活动节点失败
    const PASSPORT_RETURN_ACTIVATE_HARF_FALSE = 7112; //用户中心返回激活失败0.5%
    const PASSPORT_RETURN_ACTIVATE_ONE_FALSE = 7113; //用户中心返回激活失败1%
    const LADDER_DATA_EXCEPTION = 7114; //阶梯加息数据异常

    protected static $errorArray = array(
        self::API_MIS_PARAMS => "缺少必要参数",
        self::API_ILLEGAL => "非法请求", //无效的绑卡请求
        self::API_BUSY => "操作太频繁啦，休息一下",
        self::API_FAILED => "接口调用失败",
        self::API_EXPIRE => "接口请求过期",
        self::API_SMS => "短信发送失败",
        self::API_ERROR_PARAMS => "参数个数或值错误",

        self::VALID_CAPTCHA_FAIL => "图片验证码验证失败",
        self::VALID_SMS_FAIL => "短信验证码验证失败",
        self::VALID_PWD_FAIL => "密码格式错误",
        self::VALID_PHONE_FAIL => "手机号码格式错误",
        self::VALID_PHONE_USED => "手机号码已被注册",
        self::VALID_BANKCARD_FAIL => "银行卡号格式错误",
        self::VALID_TOKEN_FAIL => "用户未登录",
        self::VALID_NUMERICAL_FAIL => "充值金额不合法",
        self::VALID_ATTENTIONS_FAIL => "手机号错误",
        self::VALID_WITHDRAW_FAIL => "账户可转出金额不足，请确认后转出",
        self::VALID_POR_FAIL => "奖励用户不合法",
        self::VALID_AGE_FAIL => "抱歉，不支持未成年人投资",


        self::INPUT_CAPTCHA_MIS => "请识别图形验证码",
        self::INPUT_SMSCODE_MIS => "请填写短信验证码",
        self::INPUT_VERIFY_FAIL => "手机号或密码错误",
        self::INPUT_PWD_ERROR => "密码输入错误",
        self::INPUT_OLDPWD_ERROR => "原始密码输入错误",
        self::INPUT_INCONSISTENT => "两次输入的密码不一致",
        self::INPUT_USERNAME_LACK => "请填写用户姓名",
        self::INPUT_ADDRESS_LACK => "请填写收货地址",
        self::INPUT_PHONE_LACK => "请填写联系电话",
        self::INPUT_IDCARD_LACK => "身份证号码格式错误，请确认信息并重试",
        self::INPUT_IDCARD_ERROR => "身份认证未通过，请确认信息并重试",
        self::INPUT_REQ_ID_MIS => "缺少绑卡参数",
        self::INPUT_CARD_ERROR => "银行卡信息错误",
        self::CARD_NOT_SUPPORT => "暂不支持该银行，请查看支持银行列表并更换",
        self::BINDCARD_FAIL => "绑卡失败",
        self::NOT_CHECKED_PROTOCOL => "同意绑卡协议后，才能绑卡",


        self::NOT_SET_TRADE_PWD => "未设置交易密码",
        self::NOT_IDENTIFY => "用户未实名认证",
        self::HAD_IDENTIFIED => "用户已实名认证",
        self::NOT_BIND_BANKCARD => "未绑定银行卡",
        self::HAD_BIND_BANKCARD => "已绑定银行卡",
        self::HAD_SET_TRADE_PWD => "已设置交易密码",
        self::USERNAME_NOT_EXIST => "用户名不存在",
        self::BIND_BANKCARD_AGAIN => "需要重新绑卡",
        self::REPEAT_IDENTIFY => "该身份信息已认证，请使用其他身份信息或联系客服",
        self::OTHER_BIND_CARD => "一张银行卡对应一个账户，该卡已被绑定，请更换并重试",
        self::HAD_CHECKED_IN => "今天已签到，请明天再来！",



        self::SAVE_USER_FAIL => "用户信息保存失败",
        self::SAVE_ADDRESS_FAIL => "收货地址保存失败",
        self::SAVE_TRADE_PWD_FAIL => "交易密码设置失败",
        self::SAVE_BIND_CARD_FAIL => "绑定银行卡失败",
        self::MODIFY_BIND_PHONE_FAIL => "绑定手机修改失败",
        self::SAVE_IDENTIFY_FAIL => "实名认证失败",
        self::SAVE_CHARGE_FAIL => "保存充值记录失败",
        self::RELIEVE_BANKCARD_FAIL => "解除绑卡失败",
        self::SAVE_SIGNIN_PWD_FAIL => "登录密码设置失败",
        self::SAVE_FEEDBACK_FAIL => "保存反馈记录失败",
        self::SAVE_WITHDRAE_FAIL => "保存提现记录失败",
        self::SAVE_NOTIFICATION_FAIL => "保存提醒配置失败",
        self::SAVE_BANKCHANNEL_FAIL => "保存银行通道失败",
        self::SAVE_CHECKIN_FAIL => "签到失败",

        self::ACCOUNT_BIND_FAIL => "绑定账号失败",
        self::ACCOUNT_UNBIND_FAIL => "解绑账号失败",
        self::ACCOUNT_NOTBIND => "账号未绑定或者已经解绑了",
        self::ACCOUNT_ALREADY_BIND => "账号已经绑定过了，请先解绑。",


        self::ACCOUNT_TRADE_FROZEN => "交易密码已被锁定,请3小时后再试",
        self::ACCOUNT_SIGNIN_FROZEN => "错误次数频繁，请3小时后重试",
        self::ACCOUNT_IDENTIFY_FROZEN => "身份认证错误次数频繁，请联系客服",
        self::REMOTE_SIGNIN => "您已在其他设备登录，请重新登录。若非您本人操作，登录后请及时修改密码以确保账户安全",

        self::SERVER_ERROR => "服务器异常，请稍后重试",

        self::CREATE_ORDER_FAIL => '创建交易订单失败',
        self::OPEN_ORDER_FAIL => '打开交易订单失败',
        self::UPDATE_ORDER_FAIL => '更新交易订单失败',
        self::UPDATE_USER_MARGIN_FAIL => '更新用户资产失败',
        self::WRITE_MARGINRECORD_FAIL => '记资产流水失败',


        self::CREATE_REFUNDORDER_FAIL => '创建还款订单失败',
        self::REFUND_INTEREST_FAIL => '利息入账失败',
        self::REFUND_INCREASE_FAIL => '加息入账失败',
        self::REFUND_EXPINTEREST_FAIL => '体验金利息入账失败',
        self::REFUND_FAIL => '还款失败',

        self::INVEST_EXPERIENCE_FAIL => '投资体验金失败',
        self::UPDATE_EXPERIENCE_FAIL => '更新体验金使用状态失败',
        self::EXPERIENCE_AMOUNT_ILLEGAL => '体验金数额不正确',
        self::EXPERIENCE_IDS_ISUSED => '此体验金已经用过了',

        self::REDPACKET_USE_FAIL => '使用红包失败',
        self::REDPACKET_RETREAT_FAIL => '退回红包失败',

        self::INTERESTCOUPON_USE_FAIL => '加息券使用失败',
        self::INTERESTCOUPON_RETREAT_FAIL => '加息券退回失败',

        self::FAIL_PRODUCT_REFUND_FAIL => '流标退款失败',

        self::UNFROZEN_FAIL => '满标审核失败',

        self::RECHARGE_FAIL => "充值失败",
        self::RECHARGE_DEALING => "充值处理中",
        self::RECHARGE_CANCEL => "充值已取消",
        self::BINDCARD_NOT_RECHARGE => "信息校验完成，但充值未成功，请重新充值",

        
        self::COUPON_UNDIFIND => "相关劵信息不存在",
        self::ACTIVATE_NODE => "获取活动节点失败",
        self::PASSPORT_RETURN_ACTIVATE_HARF_FALSE => "用户中心返回激活失败0.5%",
        self::PASSPORT_RETURN_ACTIVATE_ONE_FALSE => "用户中心返回激活失败1%",
        self::LADDER_DATA_EXCEPTION => "阶梯加息数据异常",

    );

    public function __construct($code, $data = array(), $message = "")
    {
        if (empty($message)) {
            $message = isset(self::$errorArray[$code]) ? self::$errorArray[$code] : 'error';
        }
        //异常记录
        $this->debugTrace($code, $message, $data);

        parent::__construct($code, $message, $data);
    }


    static public function dumpAllError()
    {
        var_export(self::$errorArray);
    }

    /**
     * 错误跟踪
     *
     * @param $code
     * @param $message
     * @param array $data
     */
    private function debugTrace($code, $message, $data = array())
    {
        $errormsg = "接口调用失败：错误码：" . $code;
        $errormsg .= "；错误信息：" . $message . PHP_EOL;
        if (!empty($data)) {
            $errormsg .= "额外数据：" . json_encode($data) . PHP_EOL;
        }
        $errormsg .= "追踪信息：". PHP_EOL . self::getTraceAsString();

        logs($errormsg, 'apicall_debug');
    }
}