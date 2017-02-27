## 网利宝重构用户中心---活动相关接口

接口调用方式：`RPC`

## 接口列表
- [banner图](#banner图)
- [活动列表](#活动列表)
- [系统公告](#系统公告)


年化投资额年度top10
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
                "title": "元旦抽奖",
                "img_url": "http://192.168.10.36:8001/enclosures/201607041604226826.png", //图片地址
                "link_url": "https://www.wanglibao.com/activity/app_european_cup/",   //跳转地址
                "desc": "这是title下面的描述内容",
                "is_hot": "1"            //是否热门活动 1：是 0：否
              },
              ...
            ]
          },
          "id": 1
        }


## 系统公告

说明：系统公告列表

##### 请求参数
	{
		"jsonrpc": "2.0",
		"method": "noticeList",
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
                "id": 3,
                "title": "公告标题test4",
                "content": "&lt;p&gt;dasdasdasdasfasdada&lt;/p&gt;",
                "link": "https://php1.wanglibao.com/app/bulletin/detail/3"
              },    
              ...
            ]
          },
          "id": 1
        }

