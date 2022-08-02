<?php require_once "lib/utils.php"; ?>
<?php if(!isCmd()): ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<?php endif; ?>
<?php
define("MAX_LOG_SIZE", 2 * 1024 * 1024);
$logPath = "./qiandao.log";
date_default_timezone_set("Asia/Shanghai"); //设置时区

$name = "default"; //输出提示名字
$html = false; //是否输出为 HTML

//判断 log 文件大小
if(!isServerless()){
	if(file_exists($logPath) && filesize($logPath) >= MAX_LOG_SIZE)
		$fp = fopen($logPath, "w");
	else
		$fp = fopen($logPath, "a");
}


//设置输出备注名字
function logSetName($str)
{
	global $name;
	$name = $str;
}

function logInfo($msg)
{
	global $name;
	output("Info", $name, $msg, "#48BB31");
}

function logError($msg)
{
	global $name;
	output("Error", $name, $msg, "#FF0006");
}

function logWarn($msg)
{
	global $name;
	output("Warning", $name, $msg, "#BBBB23");
}

//设置是否输出为着色 HTML
function logAsHtml($flag)
{
	global $html;
	$html = $flag;
}

//TODO: fclose($fp) 释放资源，避免泄露

function output($type, $sender, $str, $color)
{
	global $fp, $html;
	
	//拼接消息
	$msg = "[".date("Y-m-d h:i:s",time())."][".$sender."][".$type."] ".$str;
	
	//输出到文件
	if(!isServerless())
		fputs($fp, $msg.PHP_EOL);
	
	//上色
	if($html && $color)
		$msg = '<span style="color: '.$color.'">'.$msg.'</span>';
	
	//输出到网页
	if(isCmd())
		echo $msg.PHP_EOL;
	else
		echo $msg.PHP_EOL."<br/>";
	
	//刷新缓冲区
	flush();
}