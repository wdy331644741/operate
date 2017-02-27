<?php

//RPC 密码
defined('XXTEA_KEY') || define('XXTEA_KEY', 123123);

//用户信息数据session key
defined('USER_DATA_SKEY') || define('USER_DATA_SKEY', 'userData');

//session_container 标识
defined('DEVICE_SID_CONTAINER') || define('DEVICE_SID_CONTAINER', 'device_sid_container');

//一天的秒数
defined('DAYS_SECONDS') || define('DAYS_SECONDS', 86400);

//操作记录默认保存周期（默认一天）
defined('CYCLE_EXPIRE_TIME') || define('CYCLE_EXPIRE_TIME', 86400);

//充值最低限额
defined('BOTTOM_QUOTA') || define('BOTTOM_QUOTA', 100);
defined('BOTTOM_QUOTA_PC') || define('BOTTOM_QUOTA_PC', 100);
//提现
defined('MIN_WITHDRAD_QUOTA') || define('MIN_WITHDRAD_QUOTA', 10);
defined('MAX_WITHDRAD_QUOTA') || define('MAX_WITHDRAD_QUOTA', 30000);
defined('MAX_WITHDRAD_QUOTA') || define('MAX_DAILY_WITHDRAD', 50000);
//每月免费提现次数
defined('FREE_WITHDRAW_NUM') || define('FREE_WITHDRAW_NUM', 2);

//前缀
defined('PREFIX_REQUESTID') || define('PREFIX_REQUESTID', '2r');
defined('PREFIX_RECHARGE_ID') || define('PREFIX_RECHARGE_ID', '2o');
defined('PREFIX_WITHDRAE_ID') || define('PREFIX_WITHDRAE_ID', '2w');

//额外手续费费率
defined('EXTRA_WITHDRAW_RATE') || define('EXTRA_WITHDRAW_RATE', 0.003);

//用户特殊接口所需秘钥
defined('ACCOUNT_SECRET') || define('ACCOUNT_SECRET', 'ed732d8d5204aa2d3fed97b8589e22e7');

//银行信息接口地址
define('BANKJSONSERVICEURL','http://wl_pay.dev.wanglibao.com/admin.php?c=bankrpc');

//渠道信息接口地址
define('CHANNELSERVICEURL','http://wl_pay.dev.wanglibao.com/admin.php?c=paychannel');
//支付银行卡白名单接口
define('BANKCARDWHITE','http://wl_pay.dev.wanglibao.com/admin.php?c=bankcardwhiterpc');
//文件服务器基础地址
define("STORAGE_BASE_URL",'https://store0.wanglibao.com');
//文件展示地址
define('VIEWFILEURL','/storage.php?c=index&a=view');
//银行图片展示地址
define("BANKIMAGEURL",'http://wl_pay.dev.wanglibao.com');
