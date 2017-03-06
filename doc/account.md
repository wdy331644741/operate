## 网利宝重构用户中心---用户信息相关接口

接口调用方式：`RPC`

## 接口列表

- [获得用户基础信息](#获得用户基础信息)
- [实名信息记录](#实名认证详细记录)
- [用户加息券列表](#用户加息券列表)
- [使用加息券](#使用加息券)
- [体验金列表](#体验金列表)
- [个人中心资产数据](#个人中心资产数据)
- [个人中心签到](#个人中心签到)
- [获取用户的签到记录](#获取用户的签到记录)


用户信息相关接口*````*
----------------

接口地址
> 域名/service.php?c=account

请求方式
> post

参数类型 (`Content-Type`)
>  application/json

登录状态
> 请求需要登录状态，cookie  SESSIONID=session_id


## 获得用户基础信息

说明：获取用户基础信息

##### 请求参数
	{
		"jsonrpc": "2.0",
		"method": "profile",
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
		    "data": {
                "id": "3",
                "username": "18801301379",                //用户名
                "phone": "18801301379",                   //手机号
                "realname": "郝培文",                      //真实姓名
                "display_name": "188******79",
                "gender": "1",                            //性别 0：未知 1：男  2：女
                "birthday": "1991-04-21",
                "email": "",
                "last_login": "2017-01-15 18:21:58",      //上一次登陆时间
                "last_ip": "192.168.10.53",               //上一次登陆IP
                "from_user_id": "0",                      //邀请来源用户
                "invite_code": "yzxyx3",                  //邀请码
                "from_channel": "",
                "from_platform": "PC",                    //注册平台
                "is_active": "1",                         //账号是否可用
                "create_time": "2017-01-09 10:53:07",
                "is_identify": true,                      //（bool）是否实名
                "is_bindcard": true,                      //（bool）是否绑卡
                "isset_tradepwd": true,                   //（bool）是否设置交易密码
                "withdraw_num": 0                         //（int）剩余提现次数
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

##### 所有错误情况说明

错误信息 | 错误码 | 错误说明
:--|:--|:--
用户未登录 | 1106 | 用户未登录或session已过期


## 实名认证详细记录

说明：获取用户实名认证记录

##### 请求参数

	{
    	"jsonrpc": "2.0",
    	"method": "identifyInfo",
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
            "data": {
              "name": "郝培文",        //用户姓名
              "id_number": "130429199104216215",  //身份证号
              "valid_time": "2016-06-24 17:47:19", //实名认证时间
              "valid_client": "H5"            //认证操作的客户端类型（IOS, Andriod, H5, PC 之一）
            }
          },
          "id": 1
        }
       
    失败：
        {
          "jsonrpc": "2.0",
          "error": {
            "code": 1301,
            "message": "用户未实名认证"
          },
          "id": 1
        }
        
错误信息 | 错误码 | 错误说明
:--|:--|:--
用户未登录 | 1106 | 用户未登录或session已过期
用户未实名认证 | 1301 | 用户未实名认证


## 用户加息券列表

说明：获取用户中心理财券列表

##### 请求参数

	{
        "jsonrpc": "2.0",
        "method": "couponList",
        "params": [],
        "id": 1
    }
	
> 接口返回字段见[附录1](#附录-1)

##### 返回结果

    成功：
        {
          "jsonrpc": "2.0",
          "result": {
            "code": 0,
            "message": "success",
            "getMore": "https://test.wanlgiboa.com/current/index.html",
            "data": [
              {
                  "id": "2",
                  "source_name": "手动添加",
                  "rate": "0.012",
                  "effective_start": "2012-10-16 00:00:00",
                  "effective_end": "2016-12-15 23:59:59",
                  "continuous_days": "3",
                  "is_use": 0,
                  "format_rate": "1.2%",
                  "status": "active"    //active：可用 used：已使用 expired：已过期
              },
              ....
            ]
          },
          "id": 1
        }
    
    失败:
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


## 使用加息券 
 
说明：使用加息券 
 
##### 请求参数
 
 	{
         "jsonrpc": "2.0",
         "method": "useCoupon",
         "params": [
             {
                 "coupon_id": 3687
             }
         ],
         "id": 1
     }
     
> 接口返回字段见[附录1](#附录-1)
  
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
     
     失败:
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
非法请求 | 1001 | 参数非法或缺少必要参数type
用户未登录 | 1106 | 用户未登录或session已过期


## 体验金列表

说明：获取用户体验金列表已经加息中的体验金总额

##### 请求参数

	{
        "jsonrpc": "2.0",
        "method": "experiences",
        "params": [],
        "id": 1
    }
	
> 接口返回字段见[附录1](#附录-1)

##### 返回结果

    成功：
        {
          "jsonrpc": "2.0",
          "result": {
            "code": 0,
            "message": "success",
            "useAmount": "5000.00",     //当前计息中的体验金总额
            "getMore": "https://test.wanlgiboa.com/current/index.html",
            "data": [
              {
                "name": "春节5000体验金",
                "amount": "5000.00",
                "type": 0,                          //体验金到期回收（金额前加 '-' 号）
                "datetime": "2017-01-19 14:39:49"
              },
              {
                "name": "春节5000体验金",
                "amount": "5000.00",
                "type": 1,                          //体验金入账 （金额前加 '+' 号）
                "datetime": "2017-01-15 14:39:49"
              }
            ]
          },
          "id": 1
        }
    
    失败:
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


## 个人中心签到 
 
说明：个人中心签到 
 
##### 请求参数
 
    {
        "jsonrpc": "2.0",
        "method": "checkIn",
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
             "amount": "6"          //体验金金额
           },
           "id": 1
         }
     
     失败:
     1,
         {
           "jsonrpc": "2.0",
           "error": {
             "code": 1310,
             "message": "今天已签到，请明天再来！"
           },
           "id": 1
         }
    2,
      {
        "jsonrpc": "2.0",
        "error": {
          "code": 1680,
          "message": "体验金失效"
        },
        "id": 1
      }
错误信息 | 错误码 | 错误说明
:--|:--|:--
非法请求 | 1001 | 参数非法或缺少必要参数type
今天已签到 | 1310 | 今天已签到，请明天再来！


## 获取用户的签到记录 
 
说明：获取用户的签到记录 
 
##### 请求参数
 
    {
    	"jsonrpc": "2.0",
    	"method": "userSignInMonth",
    	"params": [{
            	    "user_id": "18",
            	    "start_date": "2017-03-01",
            	    "end_date": "2017-03-04"
            	}],
    	"id": 1
    }
    
  
##### 返回结果
 
     成功：
        {
          "jsonrpc": "2.0",
          "result": [
            {
              "id": "7",
              "type": "1",
              "create_time": "2017-03-02 17:56:51"
            }
            ......
          ],
          "id": 1
        }
     
     失败:
      {
        "jsonrpc": "2.0",
        "error": {
          "code": 1000,
          "message": "缺少必要参数"
        },
        "id": 1
      }
错误信息 | 错误码 | 错误说明
:--|:--|:--
非法请求 | 1001 | 参数非法或缺少必要参数type



## 用户补签到 
 
说明：获取用户的签到记录 
 
##### 请求参数
 
    {
    	"jsonrpc": "2.0",
    	"method": "supplementUserSignIn",
    	"params": [{
            	    "user_id": "18",
            	    "date": "2017-02-28"
            	}],
    	"id": 1
    }
    
  
##### 返回结果
 
     成功：
        {
          "jsonrpc": "2.0",
          "result": [
            {
              "id": "7",
              "type": "1",
              "create_time": "2017-03-02 17:56:51"
            }
            ......
          ],
          "id": 1
        }
     
     失败:
      {
        "jsonrpc": "2.0",
        "error": {
          "code": 1000,
          "message": "缺少必要参数"
        },
        "id": 1
      }
错误信息 | 错误码 | 错误说明
:--|:--|:--
非法请求 | 1001 | 参数非法或缺少必要参数type




