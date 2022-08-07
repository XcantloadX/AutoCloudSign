<?php
require_once __DIR__."/lib/confhelper.php";

if(!file_exists("conf.php")){
    
    ConfHelper::init();
    echo "已创建默认 conf.php。\n";
    echo "请修改配置后重新运行。\n";
    exit;
}
ConfHelper::update();


require_once __DIR__."/conf.php";
require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/lib/utils.php";
require_once __DIR__."/lib/http.php";
require_once __DIR__."/lib/timewatch.php";
require_once __DIR__."/lib/log.php";
require_once __DIR__."/lib/notification.php";
require_once __DIR__."/lib/accountmanager.php";
require_once __DIR__."/lib/scriptmanager.php";

require_once __DIR__."/script/base.php";

use AccountManager as AM;
use ScriptManager as SM;
//阿里云函数会在此处报常量重复定义错误，所以先判断一下
//https://www.cnblogs.com/tochw/articles/15882179.html
if(!defined("SCRIPT_PATH"))
    define("SCRIPT_PATH", __DIR__."/script/");

set_time_limit(0); //设置脚本执行时间无上限
ignore_user_abort(true); //后台运行
date_default_timezone_set("Asia/Shanghai"); //设置时区
AM\setPath(__DIR__);
SM\setPath(__DIR__);

//通知推送配置
$nBuilder = new NotificationBuilder();
$nBuilder->setTitle("".date("Y.m.d")." 签到报告");
$nBuilder->append(date("Y.m.d")." 签到报告", "[%s]", "# %s");
$nBuilder->append("");

//GET 参数处理
$site = "all";
if(isset($_GET["site"]))
    $site = $_GET["site"];

//TODO 修复此功能
if($site != "all"){
    if(file_exists(SCRIPT_PATH."/".$site.".php")){
        include(SCRIPT_PATH."/".$site.".php");
        $nBuilder->push();
    }
    else {
        header("HTTP/1.1 400");
        echo "Invalid site name.";
    }
    exit;
}

//获取所有签到脚本
$files = scandir(SCRIPT_PATH);

//遍历导入并运行所有脚本
foreach($files as $file)
{
    if($file != "." && $file != ".." && $file != "base.php" && strpos($file, ".php") > 0){
        $path = SCRIPT_PATH."/".$file;
        
        try {
            $attrs = SM\getAttributes_($path);
        } catch (SM\ScriptException $e) {
            logError($e);
            logError("已跳过。");
            continue;
        }

        $_id = $attrs["id"];
        $_name = $attrs["name"];

        //导入脚本
        include_once($path); 

        if(!isset(ScriptStorage::$$_id)){
            logError("脚本 ID 为 $_id 的脚本在 ScriptStorage 中无配置项，请检查 ConfHelper 是否正常调用。");
            continue;
        }
        $accounts = &ScriptStorage::$$_id; //获取配置项，引用传递
        if(count($accounts) <= 0)
            continue;

        logSetName($_id);
        logInfo("开始签到$_name");
        watchStart();
        $nBuilder->append($_name, "---%s---", "## %s");

        //运行
        //TODO 检查类 $_id 是否存在
        $ins = new $_id(); //名为变量 $_id 的值的类
        $ins->setNotification($nBuilder); //设置通知推送
        
        for($i = 0; $i < count($accounts); $i++){
            $ins->run(0, $accounts[$i]); //运行 run() 方法
        }
            
        watchEnd();
        logInfo("耗时 ".watchGetSec()." 秒。");
    
    }
}
logSetName("Default");
logInfo("已完成所有签到任务");
$nBuilder->push(); //推送通知
ConfHelper::save(); //保存 conf.php
logInfo("完成");