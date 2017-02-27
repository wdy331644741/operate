# wl_passport
网利宝用户中心重构（PHP）

install
-------

```bash
    git clone git@192.168.20.240:haopeiwen/hq_passport.git
    cd hq_passport
    git clone git@192.168.20.240:liuqi/wl_framework.git system
```

全局错误码参照表
--------------
[全局错误码](doc/errcode.md)
 
接口列表
-------

- [登录、注册服务相关接口](doc/auth.md#_3)
    - [登陆页面，短信验证码](doc/auth.md#发送短信或语音验证码)
    - [登录接口](doc/auth.md#登录)
    - [撤销登录](doc/auth.md#撤销登录)
    - [用户登陆状态](doc/auth.md#用户登陆状态)
- [账号安全相关接口](doc/secure.md#_2)
    - [是否设置交易密码](doc/secure.md#是否设置交易密码)
    - [设置交易密码](doc/secure.md#设置交易密码)
    - [记得原交易密码](doc/secure.md#记得原交易密码)
    - [忘记交易密码](doc/secure.md#忘记交易密码)
    - [修改交易密码](doc/secure.md#修改交易密码)
    - [交易密码验证](doc/secure.md#交易密码验证用于支付或充值)
- [银行卡相关接口](doc/bank.md#_2)
    - [获取银行卡信息](doc/bank.md#获取银行卡信息)
    - [绑定银行卡（发送验证码）](doc/bank.md#绑定银行卡发送验证码)
    - [绑定银行卡（确认绑卡）](doc/bank.md#绑定银行卡确认绑卡)
    - [获取用户绑卡列表](doc/bank.md#获取用户绑卡列表)
    - [支持的银行列表](doc/bank.md#支持的银行列表)
- [用户信息相关接口](doc/account.md#_2)
    - [获得用户基础信息](doc/account.md#获得用户基础信息)
    - [实名信息记录](doc/account.md#实名认证详细记录)
    - [用户加息券列表](doc/account.md#用户加息券列表)
    - [使用加息券](doc/account.md#使用加息券)
    - [体验金列表](doc/account.md#体验金列表)
    - [个人中心资产数据](doc/account.md#个人中心资产数据)
- [用户资产相关接口](doc/funds.md#_2)
    - [转入转出资产数据](doc/funds.md#转入转出资产数据)
    - [账户充值](doc/funds.md#账户充值)
    - [确认充值](doc/funds.md#确认充值)
    - [充值结果查询](doc/funds.md#充值结果查询)
    - [用户提现](doc/funds.md#用户提现)
    - [提现费用](doc/funds.md#提现费用)
    - [交易记录](doc/funds.md#交易记录)

   
    

接口列表(刘&王)
---------

- [产品相关&交易相关](doc/trade.md)
- [第三方接入接口](doc/thirdparty.md)
- [奖励相关](doc/reward.md)
- [资产&收益相关](doc/margin.md)
- [JsonRpc加密说明](doc/encrypt.md)
