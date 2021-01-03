<?php
logSetName("Bilibili");
logInfo("开始签到哔哩哔哩");
watchStart();

//遍历所有 Cookie，签到
foreach($bilibili as $b)
{
	signin($b);
}

watchEnd();
logInfo("耗时 ".watchGetSec()." 秒。");
logInfo("完成");

//TODO: 解决此签到只会增加硬币，不会完成每日登录经验
//签到
function signin($cookie)
{
	//带 Cookie 访问该 API 就可以签到
	
	$ret = hGet(array(
		"url" => "http://api.bilibili.com/x/web-interface/nav",
		"cookie" => $cookie
	));
	$json = json_decode($ret);
	
	if($json->code != 0)
	{
		logError("出现错误！code=".$json->code);
		logError("返回 json: ".$ret);
		
		return;
	}
	
	logInfo("账号 @".$json->data->uname." 签到成功");
	logInfo("硬币数量：".getCoinNum($cookie));
}


//获取硬币数量
function getCoinNum($cookie)
{
	$json = hGet(array(
		"url" => "http://account.bilibili.com/site/getCoin",
		"cookie" => $cookie
	));
	
	$json = json_decode($json);
	return $json->data->money;
}