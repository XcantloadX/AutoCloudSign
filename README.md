# 云签到

使用 PHP 编写的简易云签到工具，只适合个人使用，不需要数据库，暂无 UI  

## 特性

* 支持 QQ（Qmsg）和微信（Server酱） 推送  
* 支持多账号  
* 可自定义签到脚本

## 已支持站点

| 名称      | 平台     | 备注                                               |
| ------- | ------ | ------------------------------------------------ |
| 百度贴吧    | 💻     |                                                  |
| 哔哩哔哩 主站 | 💻     | 硬币+1                                             |
| 哔哩哔哩 直播 | 💻     | 辣条&经验（Cookie 与主站相同）                              |
| 哔哩哔哩 漫画 | 📱     | 签到七天，漫券+1（Cookie 与主站相同）                          |
| ~~毕方~~  | ~~💻~~ | [bigfun停服公告](https://www.bigfun.cn/post/2165604) |
| QQ 音乐   | 📱     | 用网页版的 Cookie 也可以，**注意签到奖励不会自动领取（需要验证码）**         |
| 网易云音乐   | 💻📱   |                      |
| 网易云游戏   | 💻     | **收集 authorization 头，而不是 cookie 头**              |
| 微博超话    | 💻     |                                                  |
| 精易论坛    | 💻     | 不能用国外 IP                                         |
| 原神 国际服  | 💻     | 邮件有效期 30 天                                       |

\* 平台指签到 API 抓取的平台，并不一定代表移动端、PC 端不通用。

## 安装

1. Clone 项目到任意一个支持 curl 的 PHP 空间/VPS 上  
2. **先运行一次 `start.php`**
3. 按照注释修改`conf.php`的内容，填好 cookie  
4. 可选：搭建 PHP 服务器
5. 若搭建了服务器，定时访问`http://localhost/start.php`；若没有，定时运行 `php start.php`

**要求 PHP 版本 >= 7**  
**详细步骤见 [安装 - Wiki](https://github.com/XcantloadX/AutoCloudSign/wiki/%E5%AE%89%E8%A3%85%E6%95%99%E7%A8%8B)**

## 自定义

本项目支持自定义签到脚本  
新建一个 php 脚本放在 `/script` 目录下  

```php
<?php
//@id   //脚本ID
//@name //脚本名称
//@icon //脚本图标，目前保留
//@site //目标网站域名，目前保留
//@note //脚本备注，可以在此处写关于自定义设置的说明


//类名必须和 ID 一样，大小写必须相同！
//文件名必须和 ID 一样，而且文件名必须全小写！
class NAME extends Runner {
    public function run(string $aid, array &$data) {
        //新建的脚本需要先运行一次 start.php 才会在 conf.php 里自动创建配置项

        /* $aid 为账号 ID（account id），暂时未使用
        * $data 为对应账号的储存的数据，即 conf.php 里 ScriptStorage 类里储存的数据，结构如下
        * $data = ["cookie" => "账号1", "data1" => "自定义数据1", ...];
        * 脚本对 $data 做的修改会被保存回 conf.php 中
        */

        parent::run($aid, $data);
        //签到...
        //.................
        $this->session->get("URL_HERE", array("HEADER" => "VALUE")); //GET 请求
        $this->session->post("URL_HERE", array("HEADER" => "VALUE"), "DATA"); //POST 请求
        //HTTP 请求头里不需要加 Cookie，Cookie 会自动设置
        //若有每个请求都需要的请求头，调用 $this->session->headers["xxxx"] = "xxxx";

        //$this->notification 为通知推送类，用法参考 /lib/notification.php
        $this->notification->append("账号 @".$data["name"]." 签到成功", "%s", "### %s");
    }
}
```

## 参考项目

* [BiliExp](https://github.com/MaxSecurity/BiliExper)
* [netease-cloud-api](https://github.com/ZainCheung/netease-cloud-api)
