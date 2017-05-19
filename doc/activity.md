## 活动中心---活动相关接口 kim

接口调用方式：`RPC`

## 接口列表
- [banner图](#banner图)
- [活动列表](#活动列表)
- [系统公告](#系统公告)
- [阅读公告](#阅读公告)


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
                "img_url": "126f3713441c43c2ec66615ebd18f016.jpg",
                "link_url": "https:/test.wanglibao.com/wechat/share/index.html",
                "start_time": "2017-02-01 00:00:00",
                "end_time": "2017-06-23 11:50:00",
                "desc": "",
                "status": "1"   //正在進行中的活動
              },
              {
                "id": "2",
                "title": "oooooooooo",
                "img_url": "b59d6295c11e335fb555211d8a0d96f3.jpg",
                "link_url": "/Newinvite/newinvite.html",
                "start_time": "2017-02-28 05:25:00",
                "end_time": "2017-03-23 10:35:00",
                "desc": "",
                "status": "-1"  //过期的活动
              },
              {
                "id": "3",
                "title": "1",
                "img_url": "126f3713441c43c2ec66615ebd18f016.jpg",
                "link_url": "http://operate.wanglibao.com/admin.php?c=activity&a=add",
                "start_time": "2017-03-01 10:50:00",
                "end_time": "2017-03-09 10:50:00",
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


## 阅读公告

说明：阅读公告后，用户阅读次数加1

##### 请求参数
    {
      "jsonrpc": "2.0",
      "method": "noticeContent",
      "params": [
          {
             "article_id": 1 //公告id
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
            "data": [
              {
                "content": "&lt;p class=&quot;p&quot; style=&quot;white-space: normal;&quot;&gt;在春天里播种希望 王磊测试在春天里播种希望&lt;/p&gt;&lt;p class=&quot;p1&quot; style=&quot;white-space: normal;&quot;&gt;尊敬的用户：&lt;/p&gt;&lt;p class=&quot;p2&quot; style=&quot;white-space: normal;&quot;&gt;您好！&lt;/p&gt;&lt;p class=&quot;p3&quot; style=&quot;white-space: normal;&quot;&gt;搜狐韩娱讯 韩国演员具惠善为某时尚杂志拍摄的最新画报23日公开。具惠善完美消化了色彩淡雅的造型，展现出如春日女神般清新迷人的魅力，目光中尽显柔情暖意。具惠善完美消化了色彩淡雅的造型，展现出如春日女神般清新迷人的魅力，目光中尽显柔情暖意。图具惠善完美消化了色彩淡雅的造型，展现出如春日女神般清新迷人的魅力，目光中尽显柔情暖意。bnt新闻/供稿 王容/文Singles/图具惠善完美消化了色彩淡雅的造型，展现出如春日女神般清新迷人的魅力，目光中尽显柔情暖意。&lt;/p&gt;&lt;p class=&quot;p4&quot; style=&quot;white-space: normal;&quot;&gt;赚呗运营团队&lt;/p&gt;&lt;p class=&quot;p5&quot; style=&quot;white-space: normal;&quot;&gt;2017年3月28日&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;"
              }
            ],
            "userid": "220",
            "message": "success"
          },
          "id": 1
        }

错误信息 | 错误码 | 错误说明
:--|:--|:--
缺少必要参数 | 1000 | 缺少接口所需参数