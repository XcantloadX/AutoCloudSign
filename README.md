# 云签到
使用 PHP 编写的简易云签到工具，只适合个人使用，不需要数据库  

## 特性
* 支持 QQ（Qmsg）和微信（Server酱） 推送  
* 支持多账号  
* 已支持站点  
    * 百度
        * 百度贴吧
    * 哔哩哔哩
        * 主站 硬币签到
        * 直播
        * 漫画
        * [毕方](https://bigfun.cn)（Cookie 与B站独立）
    * 网易云音乐

## 安装
1. Clone 项目到任意一个支持 curl 的 PHP 空间/VPS 上  
2. 按照注释修改`conf.php`的内容，在`cookies.php`里填好 cookie  
3. 可选：搭建 PHP 服务器
4. 若搭建了服务器，定时访问`http://localhost/start.php`；若没有，定时运行 `php start.php`

**要求 PHP 版本 >= 7.1**
**详细步骤见 [安装 - Wiki](https://github.com/XcantloadX/AutoCloudSign/wiki/%E5%AE%89%E8%A3%85)**
## 自定义
本项目支持自定义签到脚本  
新建一个 php 脚本放在 `/script` 目录下  

```php
<?php
//@id Example //脚本ID
//@name 示例脚本 //脚本名称
//@site example.com //目标网站域名，这个目前没用

//类名必须和 ID 一样！
class Example implements Runner{
    public function run()
    {
        //$COOKIE 变量来自 cookies.php，你可以在那里面添加新的变量
        //$nBuilder 为通知推送类，用法参考 /lib/notification.php
        global $COOKIE, $nBuilder; 
        //在这里写代码...

    }
}
```


## 参考项目
* [BiliExp](https://github.com/MaxSecurity/BiliExper)
* [netease-cloud-api](https://github.com/ZainCheung/netease-cloud-api)