<?php
include("ajax.php");
define("ZHIDAO_API", "https://zhidao.baidu.com/ihome/api/signInfo");
$cookie = file_get_contents("COOKIES");
header("Content-type: application/json;charset=utf-8");

echo ajax(array(
	"url" => ZHIDAO_API,
	"cookie" => $cookie
));
?>