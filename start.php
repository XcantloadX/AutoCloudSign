<?php
include_once "lib/http.php";
include_once "lib/timewatch.php";
include_once "lib/log.php";
include_once "conf.php";
include_once "cookies.php";
define("SIGN_SCRIPT_PATH", "script/");

set_time_limit(0); //设置脚本执行时间无上限
ignore_user_abort(true); //后台运行
date_default_timezone_set("Asia/Shanghai"); //设置时区

$site = "all";
if(isset($_GET["site"]))
    $site = $_GET["site"];

if($site != "all"){
    if(!file_exists(SIGN_SCRIPT_PATH."/".$site))
        include(SIGN_SCRIPT_PATH."/".$site.".php");
    else {
        header("HTTP/1.1 400");
        echo "Invalid site name.";
    }
    exit;
}

//获取所有签到脚本
$files = scandir(SIGN_SCRIPT_PATH);

//遍历执行
foreach($files as $file)
{
	if($file != "." && $file != ".." && strpos($file, ".php") > 0)
		include(SIGN_SCRIPT_PATH."/".$file);
}