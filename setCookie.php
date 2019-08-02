<?php
if(isset($_POST["cookie"]))
{
	$cookie = $_POST["cookie"];
}
else if(file_get_contents('php://input') != "")
{
	$cookie = str_replace("cookie=", "", file_get_contents('php://input'));
}
else
{
	header("HTTP/1.1 400");
	echo "缺少参数 cookie！";
	exit;
}

if(file_put_contents("COOKIES", $cookie) <= 0)
{
	header("HTTP/1.1 400");
	echo "保存失败！";
}
else
	echo "保存成功！";
?>