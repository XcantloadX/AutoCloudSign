<?php
$data = array();
$data["err"] = 0;
$data["msg"] = "操作成功";

if(file_exists("log.txt"))
	$data["log"] = file_get_contents("log.txt");
else
	$data["log"] = "";


Post();

function Post()
{
	global $data;
	
	if($data["err"] != 0)
		header("HTTP/1.1 400");
	
	echo json_encode($data);
	exit;
}
?>