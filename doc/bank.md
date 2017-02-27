## 网利宝重构用户中心---用户信息相关接口

接口调用方式：`RPC`

## 接口列表

- [获取银行卡信息](#获取银行卡信息)
- [绑定银行卡（发送验证码）](#绑定银行卡发送验证码)
- [绑定银行卡（确认绑卡）](#绑定银行卡确认绑卡)
- [获取用户绑卡列表](#获取用户绑卡列表)
- [支持的银行列表](#支持的银行列表)


银行卡相关接口
----------------

接口地址
> 域名/service.php?c=account

请求方式
> post

参数类型 (`Content-Type`)
>  application/json

登录状态
> 请求需要登录状态，cookie  SESSIONID=session_id


## 获取银行卡信息

说明：根据银行卡号码，获取银行卡相关信息

##### 请求参数

	{
    	"jsonrpc": "2.0",
    	"method": "cardInfo",
    	"params": [
    		{	
    			"cardno"  : "6214830121363633"
    		}
    	],
    	"id": 1
    }

##### 返回结果

	成功：
        {
          "jsonrpc": "2.0",
          "result": {
            "code": 0,
            "message": "success",
            "data": {
                "bankcode": "CMBCHINA",        //银行代码（晓明负责维护）
                "bankname": "招商银行",         //银行名称
                "cardno": "6214830121363633",  //银行卡号
                "cardtype": 1,                 //银行类型（1：储蓄卡  2：信用卡 -1 未知银行卡）
                "isvalid": 1,                  //卡号是否支持
                "times_quota": 10000,          //单次限额
                "days_quota": 100000           //单日限额
                "first_quota": 10000,          //首次限额
                "bank_logo": "http://wl_pay.dev.wanglibao.com/static/images/bank-logos/zsyh.png", //logo
                "smsconfirm": 0,               //充值是否需要短信验证码（0：不需要  1：需要）
                "bottom_quota": 10,            //充值最低限额
                "min_quota": 50,                //最低提现额度
                "max_quota": 500000            //最高提现额度
            }
          },
          "id": 1
        }
       
    失败：
        {
		  "jsonrpc": "2.0",
		  "error": {
		    "code": 1106,
		    "message": "用户未登录"
		  },
		  "id": 1
		}
        
错误信息 | 错误码 | 错误说明
:--|:--|:--
用户未登录 | 1106 | 用户未登录或session已过期
暂不支持该银行，请查看支持银行列表并更换 | 1213 | 暂不支持该银行


## 绑定银行卡（发送验证码）

说明：绑定银行卡第一步，发送短信验证码

##### 请求参数

	{
		"jsonrpc": "2.0",
		"method": "bindBankCard",
		"params": [
			{	
			    "name"    : "郝培文",
			    "id_number": "130429199104216215",
				"cardno"  : 6212260200052528136,
				"phone": "18801301379"，
				"bankcode": "CMBCHINA",        //银行代码
                "bankname": "招商银行"         //银行名称
			}
		],
		"id": 1
	}

##### 返回结果

	成功：
        {
		  "jsonrpc": "2.0",
		  "result": {
		    "code": 0,
		    "message": "success",
		    "requestid": "r1468923269876004258"
		  },
		  "id": 1
		}
       
    失败：
        {
		  "jsonrpc": "2.0",
		  "error": {
		    "code": 1304,
		    "message": "已绑定银行卡"
		  },
		  "id": 1
		}
        
错误信息 | 错误码 | 错误说明
:--|:--|:--
真实姓名或身份证号不为空 | 1000 | 缺少必要参数
真实姓名或身份证号不为空 | 1000 | 缺少必要参数
手机号或银行卡号不为空 | 1000 | 缺少必要参数
手机号码格式错误 | 1103 | 用户未登录或session已过期
用户未登录 | 1106 | 用户未登录或session已过期
身份证号码格式错误，请确认信息并重试 | 1209 | 身份证号码格式错误
请填写正确的银行卡号 | 1105 | 银行卡号不正确
手机号码格式错误 | 1103 | 手机号码格式错误
暂不支持该银行 | 1213 | 通道不支持该银行
用户已实名认证 | 1302 | 用户已实名认证
该卡已被绑定 | 1302 | 该卡已被绑定
已绑定银行卡 | 1304 | 已绑定过银行卡，暂时支持绑定一个
绑定银行卡失败 | 1403 | mysql写入失败



## 绑定银行卡（确认绑卡）

说明：绑定银行卡第二步，确定绑卡操作

##### 请求参数

	{
		"jsonrpc": "2.0",
		"method": "confirmBandCard",
		"params": [
			{	
				"requestid"  : "A9AB6BF2-05D5-20A1-9ED1-770CACECE342",
				"validCode": "1234"
			}
		],
		"id": 1
	}

##### 返回结果

	成功：
        {
		  "jsonrpc": "2.0",
		  "result": {
		    "code": 0,
		    "message": "success"
		  },
		  "id": 1
		}
       
    失败：
        {
		  "jsonrpc": "2.0",
		  "error": {
		    "code": 1001,
		    "message": "非法请求"
		  },
		  "id": 1
		}
        
错误信息 | 错误码 | 错误说明
:--|:--|:--
缺少必要参数 | 1000 | 缺少必要参数
非法请求 | 1001 | 绑卡第一步操作未完成
用户未登录 | 1106 | 用户未登录或session已过期
银行卡已绑定 | 1304 | 已绑定过银行卡，暂时支持绑定一个
绑定银行卡失败 | 1403 | mysql写入失败


## 获取用户绑卡列表

说明：获取用户绑卡列表

##### 请求参数

	{
        "jsonrpc": "2.0",
        "method": "bindCardList",
        "params": [],
        "id": 1
    }

##### 返回结果

	成功：
        {
          "jsonrpc": "2.0",
          "result": {
            "code": 0,
            "message": "success",
            "data": [
              {
                "id": "1",
                "user_id": "83730",             //用户id
                "bankcode": "CMBCHINA",         //银行类型
                "bankname": "招商银行",         //银行名称
                "realname": "郝培文",           //用户真实姓名
                "cardno": "6214830121363633",  //银行卡号
                "phone": "18801301379",        //预留手机号
                "status": "1",                 //绑卡状态（0：未绑定  1：已绑定）
                "bottom_quota": 10,            //充值最低限额
                "first_quota": 10000,          //首次限额
                "times_quota": 10000,          //单次限额
                "days_quota": 100000           //单日限额
                "smsconfirm": 0,               //充值是否需要短信验证码（0：不需要  1：需要）
                "bank_logo": "http://wl_pay.dev.wanglibao.com/static/images/bank-logos/zsyh.png", //logo
                "min_quota": 50,                //最低提现额度
                "max_quota": 500000            //最高提现额度
              },
              ....
            ]
          },
          "id": 1
        }
       
    失败：
        {
		  "jsonrpc": "2.0",
		  "error": {
		    "code": 1303,
		    "message": "未绑定银行卡"
		  },
		  "id": 1
		}
        
错误信息 | 错误码 | 错误说明
:--|:--|:--
用户未登录 | 1106 | 用户未登录或session已过期
未绑定银行卡 | 1303 | 用户没有绑定银行卡


## 支持的银行列表

说明：获取支持的银行列表

##### 请求参数

	{
    	"jsonrpc": "2.0",
    	"method": "supportBankList",
    	"params": [{
    	    "type": 1   //1：pc端  2：app(默认值，可以省略)
    	}],
    	"id": 1
    }

##### 返回结果

	成功：
        {
          "jsonrpc": "2.0",
          "result": {
            "code": 0,
            "message": "success",
            "data": [
              {
                "bank_code": "CIB",
                "bank_name": "兴业银行",
                "bottom_quota": 10,            //充值最低限额
                "first_quota": 10000,          //首次限额
                "times_quota": 10000,          //单次限额
                "days_quota": 100000,          //单日限额
                "bank_logo": "http://wl_pay.dev.wanglibao.com/82126f6ac91fb91754cb032a701bf31f.jpg"
              },
              ....
            ]
          },
          "id": 1
        }
