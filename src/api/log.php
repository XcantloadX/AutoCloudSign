<?php
$data = array();
$data["code"] = 0;
$data["msg"] = "操作成功";

//获取日志
if($_SERVER["REQUEST_METHOD"] == "GET")
{
	if(file_exists("log.txt"))
		$data["log"] = file_get_contents("log.txt");
}

//清除日志
else if($_SERVER["REQUEST_METHOD"] == "DELETE")
{
	if(file_exists("log.txt"))
	{
		$data["msg"] = "清除成功";
		if(!unlink("log.txt"))
		{
			$data["code"] = -2;
			$data["msg"] = "清除失败";
		}
	}
}

Send();

function Send()
{
	global $data;
	
	if($data["code"] != 0)
		header("HTTP/1.1 400");
	header("Content-Type: application/json");
	
	echo json_encode($data);
	exit;
}
?>