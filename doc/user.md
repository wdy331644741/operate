## 活动中心---相关接口

接口调用方式：`RPC`

## 接口列表

- [体验金列表](#体验金列表)
- [个人中心资产数据](#个人中心资产数据)
- [个人中心签到](#个人中心签到)
- [获取用户的签到记录](#获取用户的签到记录)
- [我的佣金](#我的佣金)

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
非法请求 | 1001 | 参数非法或缺少必要参数type
今天已签到 | 1310 | 今天已签到，请明天再来！


## 获取用户的签到记录 
 
说明：获取用户的签到记录 
 
##### 请求参数
 
    {
    	"jsonrpc": "2.0",
    	"method": "userSignInMonth",
    	"params": [],
    	"id": 1
    }
    
  
##### 返回结果
 
     成功：
        {
          "jsonrpc": "2.0",
          "result": {
            "code": 200,
            "continueDays": "2",
            "today": "2017年03月14日",
            "stringData": [
              0,
              0,
              0,
              0,
              0,
              0,
              0,
              0,
              0,
              0,
              0,
              0,
              1,
              1,
              0,
              0,
              "s1",
              0,
              0,
              0,
              0,
              "s2",
              0,
              0,
              0,
              0,
              0,
              0,
              0,
              0,
              0
            ],
            "today_check": true,
            "userSignInData": "",
            "data": [
              {
                "time": "2017-03-01",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-02",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-03",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-04",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-05",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-06",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-07",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-08",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-09",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-10",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-11",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-12",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-13",
                "check_in": 1,
                "gift_check": 0
              },
              {
                "time": "2017-03-14",
                "check_in": 1,
                "gift_check": 0
              },
              {
                "time": "2017-03-15",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-16",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-17",
                "check_in": 0,
                "gift_check": "s1"
              },
              {
                "time": "2017-03-18",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-19",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-20",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-21",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-22",
                "check_in": 0,
                "gift_check": "s2"
              },
              {
                "time": "2017-03-23",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-24",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-25",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-26",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-27",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-28",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-29",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-30",
                "check_in": 0,
                "gift_check": 0
              },
              {
                "time": "2017-03-31",
                "check_in": 0,
                "gift_check": 0
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
非法请求 | 1001 | 参数非法或缺少必要参数type



## 用户补签到 
 
说明：用户补签到  
 
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
            "code": 1106,
            "message": "用户未登录"
          },
          "id": 1
      }
错误信息 | 错误码 | 错误说明
:--|:--|:--
非法请求 | 1001 | 参数非法或缺少必要参数type


## 共享好友收益 
 
说明：获取用户的签到记录 
 
##### 请求参数
 
        {
            "jsonrpc": "2.0",
            "method": "friendsShareEarnings",
            "params": [{
                        "userId": "18",
                        "uesCashTotal": "9999",
                        "uesInterestCouponTotal": "9999",
                        "amount": "9999",
                        "type": "revenueSharing",
                        "beginTime": "2017-02-28",
                        "endTime": "2017-02-28"
                    }],
            "id": 1
        }
  
##### 返回结果
 
     成功：
         
     {
       "jsonrpc": "2.0",
       "result": {
         "code": "200",
         "message": "执行成功"
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
非法请求 | 1001 | 参数非法或缺少必要参数type



## 用户收益明细 
 
说明：用户收益明细 
 
##### 请求参数
 
       {
       	"jsonrpc": "2.0",
       	"method": "userProceedsDetailed",
       	"params": [],
       	"id": 1
       }
  
##### 返回结果
 
     成功：
     {
       "jsonrpc": "2.0",
       "result": {
         "code" : 200,
         "experience_amount": "0.00",
         "revenue_sharing_amount": "998.0000000000",
         "promoter_status":"1", //推广员状态 -1未申请  0申请待审核 1通过审核  2不通过
         "data": [
           {
             "recharge": false,
             "id": "11",
             "username": "18801301379",
             "display_name": "188******79",
             "amount": 0,  //该好友已获得到的收益
             "invest": 0  //好友总资产
           },
           {
             "recharge": true,
             "amount": "998.0000000000",
             "id": "10",
             "username": "18646003680",
             "display_name": "186******80",
             "amount": 0,
             "invest": 0
           }
           ..........
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
非法请求 | 1001 | 参数非法或缺少必要参数type

## 判断用户当天是否签到 
 
说明：判断用户当天是否签到 
 
##### 请求参数
 
       {
       	"jsonrpc": "2.0",
       	"method": "checkTodaySignIn",
       	"params": [],
       	"id": 1
       }
  
##### 返回结果
 
     成功：
     {
       "jsonrpc": "2.0",
       "result": {
         "code": 200,
         "status": false    // true/false
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
非法请求 | 1001 | 参数非法或缺少必要参数type

## 我的佣金

说明：app端我的佣金

##### 请求参数
       {
        "jsonrpc": "2.0",
        "method": "checkTodaySignIn",
        "params": [],
        "id": 1
       }
##### 返回结果

      成功：
      {
        "jsonrpc": "2.0",
        "result": {
          "code": 200,
          "message": "返回成功",
          "data": {
            "relation": [
              {
                "display_name": "130******75",
                "avaliable_amount": "30.8975608822",  //总资产
                "recharge": 1 //是否投资  0 or 1
              },
              {
                "display_name": "186******16",
                "avaliable_amount": "24.4643818841",
                "recharge": 1
              },
              {
                "display_name": "131******03",
                "avaliable_amount": null,
                "recharge": 1
              },
              ............
            ]
          },
          "earnings": 1,  //1展示20%  0展示10%共享收益
          "commission": 120  // 我的佣金
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
非法请求 | 1001 | 参数非法或缺少必要参数type

