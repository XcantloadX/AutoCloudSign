<?php
//Cookie 设置相关

$data = array();
$data["err"] = 0;
$data["msg"] = "操作成功";
$data["cookie"] = "";

//获取方法
if(!isset($_GET["method"]))
{
	$data["err"] = -1;
	$data["msg"] = "缺少参数 method";
	Post();
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
		$data["err"] = -2;
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
	
	Post();
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
		$data["err"] = -1;
		$data["msg"] = "Cookie 为空";
		Post();
	}
	
	//写入 Cookie
	if(file_put_contents("COOKIES", $cookie) <= 0)
	{
		$data["err"] = -3;
		$data["msg"] = "无法写入文件";
		Post();
	}
	else
	{
		Post();
	}
	
}

function Post()
{
	global $data;
	
	if($data["err"] != 0)
		header("HTTP/1.1 400");
	
	echo json_encode($data);
	exit;
}
?>