## 网利宝重构用户中心---内部其他模块接口调用

接口调用方式：`RPC`

## 接口列表

- [获取系统限额配置](#获取系统限额配置)


## 获取系统限额配置

说明：获取系统限额配置

##### 请求参数

	{
        "jsonrpc": "2.0",
        "method": "getQuotaConfig",
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
              "isOpenPurchase": false,          //是否开放购买
              "purchaseQuota": 50000000,        //最大买入限额（若开放购买，该值不生效，购买不做限制）
              "platformThrottle": "6.00",       //平台阀值（6%）
              "personPurchaseQuota": 100000,    //用户可购买最大额度
              "timesWithdrawQuota": 5000,       //用户单笔提现额度
              "dailyWithdrawQuota": 50000       //用户单日提现额度
              "dailyWithdrawTotal": 900000      //通道单日可用提现额度
            }
          },
          "id": 1
        }
    
    失败:
        {
          "jsonrpc": "2.0",
          "error": {
            "code": 1001,
            "message": "IP不合法"
          },
          "id": 1
        }
 
错误信息 | 错误码 | 错误说明
:--|:--|:--
IP不合法 | 1001 | 请求非法
