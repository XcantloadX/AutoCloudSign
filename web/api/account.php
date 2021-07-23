<?php
require "../../lib/accountmanager.php";
use AccountManager as AM;
AM\setPath("../../accounts.json");
header("Content-Type: application/json");

//处理 action
$action = _get("action");
switch ($action) {
	case "add": //POST
		$settings = _post("settings", false, null);
		if($settings != null)
			$settings = toJSON(urldecode($settings)); //settings=...(url 编码后的 json)
		$aid = AM\add(_post("script"), _post("name"), _post("cookie"), _post("note", false, ""),  $settings);
		echo '{"code": 0, "msg": "成功", "data": {"aid": "'.$aid.'"}}';
		break;
	case "query":
		$data = AM\query(_get("script"));
		echo toJSON(array("code" => 0, "msg" => "成功", "data" => $data));
		break;
	case "modify":
		$settings = _post("settings", false, null);
		if($settings != null)
			$settings = toJSON(urldecode($settings)); //settings=...(url 编码后的 json)
		$aid = AM\modify(_post("aid"), _post("script"), _post("name"), _post("cookie"), _post("note", false, ""),  $settings);
		echo '{"code": 0, "msg": "成功"}';
		break;
	case "delete": //POST
		$val = AM\delete(_post("aid"));
		if($val)
			echo '{"code": 0, "msg": "成功"}';
		else
			echo '{"code": -3, "msg": "指定 aid 不存在"}';
		break;
	case "get":
		$data = AM\get(_get("aid"));
		echo toJSON(array("code" => 0, "msg" => "成功", "data" => $data));
		break;
	case "clear": //POST
		AM\clear();
		echo '{"code": 0, "msg": "成功"}';
		break;
	default:
		header("HTTP/1.1 400");
		echo '{"code": -1, "msg": "action 无效"}';
		break;
}

/**
 * 取得请求 URL 参数
 * @param  string       $name     参数名
 * @param  bool|boolean $autoFail 参数不存在时自动输出错误
 * @param  mixed|null   $emptyVal 参数不存在时应该返回的值
 * @return [type]                 $emptyVal 的值
 */
function _get(string $name, bool $autoFail = true, mixed $emptyVal = null){
	if(isset($_GET[$name]))
		return $_GET[$name];
	else if($autoFail){
		header("HTTP/1.1 400");
		echo '{"code": -2, "msg": "缺少参数 '.$name.'"}';
		exit;
	}
	else
		return $emptyVal;
}

/**
 * 取得请求 POST 参数
 * @param  string       $name     参数名
 * @param  bool|boolean $autoFail 参数不存在时自动输出错误
 * @param  mixed|null   $emptyVal 参数不存在时应该返回的值
 * @return [type]                 $emptyVal 的值
 */
function _post(string $name, bool $autoFail = true, string $emptyVal = null){
	if(isset($_POST[$name]))
		return htmlspecialchars($_POST[$name]);
	else if($autoFail){
		header("HTTP/1.1 400");
		echo '{"code": -2, "msg": "缺少参数 '.$name.'"}';
		exit;
	}
	else
		return $emptyVal;
}

function toJSON($array){
	return json_encode($array, JSON_UNESCAPED_UNICODE);
}