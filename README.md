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
然后定时访问/运行`start.php`即可  

## 参考项目
* [BiliExp](https://github.com/MaxSecurity/BiliExper)
* [netease-cloud-api](https://github.com/ZainCheung/netease-cloud-api)