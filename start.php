<?php
require_once "lib/http.php";
require_once "lib/timewatch.php";
require_once "lib/log.php";
require_once "lib/notification.php";
require_once "lib/accountmanager.php";
require_once "lib/scriptmanager.php";
require_once "conf.php";
require_once "cookies.php";
require_once "script/base.php";
use AccountManager as AM;
use ScriptManager as SM;
define("SIGN_SCRIPT_PATH", "script/");

set_time_limit(0); //设置脚本执行时间无上限
ignore_user_abort(true); //后台运行
date_default_timezone_set("Asia/Shanghai"); //设置时区
AM\setPath("./");
SM\setPath("./");

//通知推送配置
$nBuilder = new NotificationBuilder();
$nBuilder->setTitle("".date("Y.m.d")." 签到报告");
$nBuilder->append(date("Y.m.d")." 签到报告", "[%s]", "# %s");
$nBuilder->append("");

//GET 参数处理
$site = "all";
if(isset($_GET["site"]))
    $site = $_GET["site"];

if($site != "all"){
    if(file_exists(SIGN_SCRIPT_PATH."/".$site.".php")){
        include(SIGN_SCRIPT_PATH."/".$site.".php");
        $nBuilder->push();
    }
    else {
        header("HTTP/1.1 400");
        echo "Invalid site name.";
    }
    exit;
}

//获取所有签到脚本
$files = scandir(SIGN_SCRIPT_PATH);

//遍历导入并运行所有脚本
foreach($files as $file)
{
    if($file != "." && $file != ".." && $file != "base.php" && strpos($file, ".php") > 0){
        $path = SIGN_SCRIPT_PATH."/".$file;
        
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

        $accounts = AM\query($_id); //获取所有账号
        //若此脚本没有对应的账号，跳过
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
        
        foreach ($accounts as $aid => $data) {
            $ins->run($aid, $data); //运行 run() 方法
        }
            
        watchEnd();
        logInfo("耗时 ".watchGetSec()." 秒。");
    
    }
}
logInfo("已完成所有签到任务");
$nBuilder->push(); //推送通知
logInfo("完成");