# 云签到
使用 PHP 编写的简易云签到工具，只适合个人使用，不需要数据库  
支持多账号  

## 特性
* 支持 Qmsg/Server酱 推送  
* 已支持站点  
    * 百度贴吧
    * 哔哩哔哩 硬币签到
    * 哔哩哔哩直播
    * 哔哩哔哩漫画
    * 网易云音乐 积分签到

## 安装
把项目上传到任意一个支持 curl 的 PHP 空间/VPS 上  
按照注释修改`conf.php`的内容，在`cookies.php`里填好 cookie  
然后定时访问`start.php`即可  

## 自定义网站
本项目支持自定义签到脚本  
新建一个 php 脚本放在 `/script` 目录下  
```php
<?php
//@id 脚本ID
//@name 脚本名称
//@site 目标网站域名，这个目前没用

//类名必须和 ID 一样！
class NAME implements Runner{
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