## 活动中心---活动相关接口 kim

接口调用方式：`RPC`

## 接口列表
- [banner图](#banner图)
- [活动列表](#活动列表)
- [系统公告](#系统公告)
- [获取首页文案展示](#获取首页文案展示)


活动相关
----------------

接口地址
> 域名/service.php?c=inside

请求方式
> post

参数类型 (`Content-Type`)
>  application/json

## banner图

说明：banner图列表（已排序）

##### 请求参数
	{
		"jsonrpc": "2.0",
		"method": "banners",
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
                "title": "投资安全",
                "img_url": "http://192.168.10.36:8001/enclosures/201607041604226826.png", //图片地址
                "link_url": "https://www.wanglibao.com/activity/app_european_cup/"   //跳转地址
              },
              ...
            ]
          },
          "id": 1
        }



## 活动列表

说明：活动列表

##### 请求参数

    {
        "jsonrpc": "2.0",
        "method": "activities",
        "params": [
            {
               "page": 1
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
          "data": [
            {
              "id": "1",
              "title": "邀请好友得双重奖励 - 白拿10000元体验金及1%加息券",
              "img_url": "https://store0.izhuanbei.com/storage.php?c=index&a=view&file=424711871b42a9cc348538b9edc45c5e.jpg&access_key=6498cb3b7f607dbfd14c703f2bbefe69&style=",
              "link_url": "https://test.izhuanbei.com/wechat//Newinvite/newinvite.html",
              "start_time": "2017-02-01 00:00:00",
              "end_time": "2017-06-23 11:50:00",
              "check_login": "1",//需要检查用户是否登录
              "desc": "",
              "status": "1" //1正在进行中的活动  -1过期的活动
            },
            {
              "id": "2",
              "title": "阶梯加息",
              "img_url": "https://store0.izhuanbei.com/storage.php?c=index&a=view&file=04d200daae452d916b72dad0dd39cee9.jpg&access_key=6498cb3b7f607dbfd14c703f2bbefe69&style=",
              "link_url": "https://test.izhuanbei.com/wechat//Newinvite/newinvite.html",
              "start_time": "2017-02-28 05:25:00",
              "end_time": "2017-07-31 23:55:00",
              "check_login": "0",
              "desc": "",
              "status": "1"
            },
            {
              "id": "3",
              "title": "1",
              "img_url": "126f3713441c43c2ec66615ebd18f016.jpg",
              "link_url": "http://operate.wanglibao.com/admin.php?c=activity&a=add",
              "start_time": "2017-03-01 10:50:00",
              "end_time": "2017-03-09 10:50:00",
              "check_login": "0",
              "desc": "",
              "status": "-1"  //过期的活动
            }
          ],
          "pagecounts": 1   //总页数
        },
        "id": 1
      }

错误信息 | 错误码 | 错误说明
:--|:--|:--
缺少必要参数 | 1000 | 缺少接口所需参数

## 系统公告

说明：系统公告列表

##### 请求参数
	{
		"jsonrpc": "2.0",
		"method": "noticeList",
		"params": [
              {
                 "page": 1
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
          "data": [
            {
              "id": "3",
              "title": "按时打算向出租车",
              "content": "afxzcvxcvdzsfgsdfgawefasdfsad阿萨德阿萨德sdf申请全文",
              "link": "https://php1.wanglibao.com/app/bulletin/detail/3",
              "readCounts": 0 //阅读次数(0代表没有阅读)
            },
            {
              "id": "2",
              "title": "国际学校2222222222222222222",
              "content": "&lt;p&gt;国际学校国际学校国际学校国际学校国际学校国际学校国际学校国际学校国际学校&lt;/p&gt;",
              "link": "https://php1.wanglibao.com/app/bulletin/detail/3",
              "readCounts": 1
            },
            {
              "id": "1",
              "title": "111111111111111111",
              "content": "&lt;p&gt;案例看电视剧福利卡萨家乐福看见爱上了咖啡姐阿斯利康都放假了卡死机付了款爱上发&lt;br/&gt;&lt;/p&gt;",
              "link": "https://php1.wanglibao.com/app/bulletin/detail/3",
              "readCounts": 9
            }
          ]
        },
        "id": 1
      }

错误信息 | 错误码 | 错误说明
:--|:--|:--
缺少必要参数 | 1000 | 缺少接口所需参数

## 获取首页文案展示

说明：默认展示default

#### 请求参数
```
{
    "jsonrpc": "2.0",
    "method": "getIndexSlogan",
    "params": [
        
    ],
    "id": 1
}
```

##### 返回结果
```
成功：
        {
            "jsonrpc": "2.0",
            "result": {
                "code": 0,
                "message": "success",
                "data": {
                    "title": "测试默认展示文案test",
                    "link_url": "https://baidu.com",
                    "display_name": "default" //default为默认状态、actively活动、notice公告
                }
            },
            "id": 1
        }

失败：
        {
        "jsonrpc": "2.0",
        "error":{
            "code": 1111,
            "message": "错误信息xxx"
        },
        "id": 1
        }
```

错误信息 | 错误码 | 错误说明
:--|:--|:--
缺少必要参数 | 1000 | 缺少接口所需参数

