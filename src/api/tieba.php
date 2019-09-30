<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<?php
define("LOG_FILE", "log.txt");

set_time_limit(0); //设置脚本执行时间无上限
date_default_timezone_set("Asia/Shanghai"); //设置时区

//log 文件
if(file_exists(LOG_FILE) && (filesize(LOG_FILE) / 1024 / 1024) >= 2) //如果文件大于 2MB 则清空
	$log = fopen(LOG_FILE, "w");
else
	$log = fopen(LOG_FILE, "a");

//读取、检查 Cookie
$cookie = file_get_contents("COOKIES");
if($cookie == "")
{
	LOG_FILE("Cookie 未设置！无法签到！");
	exit;
}

signAll($log);

//签到
//参数：贴吧名称
function sign($name)
{
	global $cookie;
	$data = ["ie" => "utf-8", "kw" => $name];
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://tieba.baidu.com/sign/add");
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36"); //设置 UA
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true); // 发送 Post 请求
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //请求参数
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //返回内容储存到变量中
	
	$json = curl_exec($ch);
	
	return $json;
}


//签到所有贴吧
//参数：输出日志的 fopen 对象
function signAll($logOut)
{
	global $cookie;
	
	$names = getAllBars();
	$signed = 0; //签到成功个数
	$t1 = microtime(true);
	
	//循环签到所有贴吧
	for($i = 0; $i < count($names); $i++)
	{
		$json = sign($names[$i]);
		$json = json_decode($json);
		
		//错误码
		$code = intval($json->no);
		
		if($code == 1101)
		{
			LOG_FILE("Warning", "你已经签到过 ".$names[$i]."吧 了！");
		}
		else if($code == 1990055)
		{
			LOG_FILE("Error", "Cookie 已失效，请重新设置！");
			LOG_FILE("Error", "返回 json：".json_encode($json));
			break;
		}
		else if($code != 0)
		{
			LOG_FILE("Error", "签到 ".$names[$i]."吧 时发生错误！");
			LOG_FILE("Error", "返回 json：".json_encode($json));
		}
		else
		{
			LOG_FILE("Info", "签到 ".$names[$i]."吧 成功。");
			$signed++;
		}
	}
	
	$t2 = microtime(true);
	LOG_FILE("Info", "已成功签到：".$signed."/".count($names)." 个贴吧。");
	LOG_FILE("Info", "耗时 ".round($t2 - $t1, 3)." 秒。");
}

//获取所有关注的贴吧的名称
function getAllBars()
{
	global $cookie;
	
	//获取贴吧首页
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://tieba.baidu.com");
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36"); //设置 UA
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$html = curl_exec($ch);
	
	$start = strpos($html, "spage/widget/forumDirectory") + 27 + 2;
	$end = strpos($html, "</script>", $start) - 2;
	$json = substr($html, $start, $end - $start);

	//解析 json
	$json = json_decode($json);
	$names = array();
	
	//遍历出所有名称
	for($i = 0; $i < count($json->forums); $i++)
	{
		array_push($names, $json->forums[$i]->forum_name);
	}
	
	return $names;
}

function LOG_FILE($type ,$str)
{
	global $log;
	$str = "[".$type."][".date("Y-m-d h:i:s",time())."] ".$str."\r\n";
	
	//输出到日志
	fwrite($log, $str);
	
	//输出到网页
	echo $str.PHP_EOL;
	echo "<br />";
	
	//刷新缓冲区
	ob_flush();
	flush();
}

?>
