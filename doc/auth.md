## 网利宝重构用户中心---登录注册页面相关接口

接口调用方式：`RPC`

## 接口列表
- [登陆页面，短信验证码](#发送短信或语音验证码)
- [登录接口](#登录)
- [撤销登录](#撤销登录)
- [用户登陆状态](#用户登陆状态)


登录注册页面相关接口
----------------

接口地址
> 域名/service.php?c=account

请求方式
> post

参数类型 (`Content-Type`)
>  application/json


## 发送短信或语音验证码

说明：用于注册页面发送短信或语音验证码，成功返回验证码。当前图片验证码可以重复验证（产品需求），不用重新获取

##### 请求参数
    //方式一：使用图片验证码示例
	{
        "jsonrpc": "2.0",
        "method": "messageAuth",
        "params": [
            {
                "mobile": "18801301379"
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
              "code": 551985,
              "key": "nb0ZlLuuF8ssX0Yy",
              "call_num": 1
            }
          },
          "id": 1
        }

	失败：
		{
          "jsonrpc": "2.0",
          "error": {
            "code": 1104,
            "message": "手机号码已经被占用"
          },
          "id": 1
        }

##### 所有错误情况说明

错误信息 | 错误码 | 错误说明
:--|:--|:--
手机号码格式错误 | 1103 | 用户填写的手机号码格式不正确

## 登录

说明：用户登录接口

##### 请求参数

	{
		"jsonrpc": "2.0",
		"method": "signin",
		"params": [
			{
				"username": "18801301379",
				"sms_key": "7bf9PzAwqi3pKEV3",
                "sms_code": 871853
			}
		],
		"id": 1
	}
	

参数名 | 是否必须 | 类型 | 备注
:--|:--|:--|:--
username | 是 | string | 用户名，同手机号码
sms_key | 是 | string | 调用[短信验证码](#发送短信或语音验证码)接口得到的 `key` 字段值
sms_code | 是 | string | 用户收到短信后，填写的短信验证码
invite_code | 否 | string | 邀请码（老平台注册字段）


##### 返回结果

	成功：
        {
          "jsonrpc": "2.0",
          "result": {
            "code": 0,
            "message": "登陆成功",
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
		    "code": 1202,
		    "message": "用户名不存在或密码错误",
		    "data": {
		        "call_num": 4
		    }
		  },
		  "id": 1
		}


> 为了防止暴力破解，在失败的情况超过规定次数（当前为三次），
> 如果call_num 值为`3`，则表示失败次数已达到限制，
> 此时客户端（或web端）应该调用图片验证码接口在登录界面加上图片验证码

##### 所有错误情况说明

错误信息 | 错误码 | 错误说明
:--|:--|:--
接口调用过于频繁 | 1002 | 接口调用过于频繁
图片验证码验证失败 | 1100 | 图片验证码错误
用户名不存在或密码错误 | 1202 | 用户名不存在或密码错误


## 撤销登录

说明：退出登录

##### 请求参数

	{
		"jsonrpc": "2.0",
		"method": "signout",
		"params": [],
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


##### 所有错误情况说明
无

## 登录状态
 
说明：获取当前登录状态
 
##### 请求参数
 
 	{
        "jsonrpc": "2.0",
        "method": "loginStatus",
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
             "status": 0   //登录状态  0：未登录  1：已登录
           },
           "id": 1
         }
     
     失败:
         无
   
