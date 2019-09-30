<?php
//Cookie 设置相关

$data = array();
$data["code"] = 0;
$data["msg"] = "操作成功";
$data["cookie"] = "";

//获取方法
if(!isset($_GET["method"]))
{
	$data["code"] = -1;
	$data["msg"] = "缺少参数 method";
	Send();
}
else
{
	$method = $_GET["method"];
	
	if($method == "get")
		TGetCookie();
	else if($method == "set")
		TSetCookie();
	else
	{
		$data["code"] = -2;
		$data["msg"] = "参数 method 不正确";
	}
}


//返回 Cookie
function TGetCookie()
{
	//如果 COOKIE 不存在则自动创建
	if(!file_exists("COOKIES"))
		fclose(fopen("COOKIES", "w"));
	global $data;
	$data["cookie"] = file_get_contents("COOKIES");
	
	Send();
}

//设置 Cookie
function TSetCookie()
{
	global $data;
	
	//获取 Cookie
	if(file_get_contents("php://input") != "")
	{
		$cookie = file_get_contents("php://input");
	}
	else
	{
		$data["code"] = -1;
		$data["msg"] = "Cookie 为空";
		Send();
	}
	
	//检查 cookie
	if(!TestCookie($cookie))
	{
		$data["code"] = -4;
		$data["msg"] = "Cookie 无效";
		Send();
	}
	
	//写入 Cookie
	if(file_put_contents("COOKIES", $cookie) <= 0)
	{
		$data["code"] = -3;
		$data["msg"] = "无法写入文件";
		Send();
	}
	else
	{
		$data["msg"] = "保存成功";
		Send();
	}
	
}

function TestCookie($cookie)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://tieba.baidu.com/f/user/json_userinfo");
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36"); //设置 UA
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //返回内容储存到变量中
	
	$data = curl_exec($ch);
	return $data != "null";
}

function Send()
{
	global $data;
	
	if($data["code"] != 0)
		header("HTTP/1.1 400");
	
	echo json_encode($data);
	exit;
}
?>