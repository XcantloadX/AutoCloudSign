# 云签到
使用 PHP 编写的简易云签到工具，只适合个人使用  
非常简易，不需要数据库，甚至连界面都没有！  

## 安装
把项目上传到任意一个支持 curl 的 PHP 空间/VPS 上  
在`conf.php`里填好 cookie
使用 IFTTT（如果是虚拟主机且不支持定时的话）或者 cron jobs 或者其他**定时访问**`start.php`  

## 截图
![签到日志](https://github.com/XcantloadX/TieBaCloudSign/blob/master/log.png?raw=true)
![IFTTT 示意图](https://github.com/XcantloadX/TieBaCloudSign/blob/master/ifttt.png?raw=true)