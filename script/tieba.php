<?php
logSetName("TieBa");
logInfo("开始签到百度贴吧");

watchStart();
//读取 Cookie
foreach($tieba as $t)
{
	signAll($t);
}

watchEnd();
logInfo("耗时 ".watchGetSec()." 秒。");
logInfo("完成");


//签到
//参数：贴吧名称
function sign($name, $cookie)
{
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
function signAll($cookie)
{
	$names = getAllBars($cookie);
	$signed = 0; //签到成功个数
	
	//循环签到所有贴吧
	for($i = 0; $i < count($names); $i++)
	{
        startSign:
		$json = sign($names[$i], $cookie);
		$json = json_decode($json);
		
		//错误码
		$code = intval($json->no);
		
		
		if($code == 1101)
		{
            //今天已签到过
		}
		//账号未登录
		else if($code == 1990055)
		{
			logError("Cookie 已失效，请重新设置！");
			logError("返回 json：".json_encode($json));
			break;
		}
        else if($code == 1102){ //您签得太快了 ，先看看贴子再来签吧:)
            logError("签到频率过快！1s 后重试...");
            sleep(1);
            goto startSign;
        }
		else if($code != 0)
		{
			logInfo("签到 ".$names[$i]."吧 时发生错误！");
			logInfo("返回 json：".json_encode($json));
		}
		else
			$signed++;
		
		usleep(100000); //= 0.1s
	}
	
	$t2 = microtime(true);
	logInfo("已成功签到：".$signed."/".count($names)." 个贴吧。");
}

//获取所有关注的贴吧的名称
function getAllBars($cookie)
{
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

?>
