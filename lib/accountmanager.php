<?php
//账号管理
//用来管理 accounts.json
//注：aid 指 accountID
namespace AccountManager;
require_once "scriptmanager.php";
use ScriptManager as SM;
$rootPath = "";
$filePath = "";

/**
 * 添加账号
 * @param string $script   使用的脚本
 * @param string $name     账号名称
 * @param string $cookie   Cookie
 * @param string $note     备注
 * @param array|null  $settings 设置，脚本自定义
 * @return string 账号的 aid
 */
function add(string $script, string $name, string $cookie, string $note, array $settings = null){
	$data = _read();
	$id = randomkeys(5);
	if(isset($data[$id])) //重复检测
		$id = randomkeys(5);
	$data[$id] = array(
		"script" => $script,
		"name" => $name,
		"cookie" => $cookie,
		"note" => $note,
		"data" => $settings
	);
	_write($data);
	return $id;
}

/**
 * 查询账号
 * @param  string|null $script 过滤目标脚本 ID
 * @return array               查询结果
 */
function query(string $script = null) {
    //TODO 修改全局变量（储存设置的方式）
    //accounts.json
    if ($GLOBALS["enableWebUI"]) {
        $data = _read();
        if ($script == null || $script == "")
            return $data;
        if ($data == null || empty($data))
            return array();
        $ret = array();
        //遍历过滤
        foreach ($data as $aid => $val) {
            if (strtolower($val["script"]) == strtolower($script))
                $ret[$aid] = $val;
        }
        return $ret;
    } //cookies.php
    else {
        $accounts = array();
        if (isset($GLOBALS[$script])) {
            foreach ($GLOBALS[$script] as $cookie) {
                array_push($accounts, array(
                    "id" => $script,
                    "name" => "",
                    "cookie" => $cookie,
                    "note" => "",
                    "data" => null
                ));
            }
        }
        return $accounts;
    }
}

/**
 * 修改账号
 * @param string $aid      账号 aid
 * @param string $script   使用的脚本
 * @param string $name     账号名称
 * @param string $cookie   Cookie
 * @param string $note     备注
 * @param array|null  $settings 设置，脚本自定义
 */
function modify(string $aid, string $script, string $name, string $cookie, string $note, array $settings = null){
	$data = _read();
	//TODO 检查是否存在
	$data[$aid] = array(
		"script" => $script,
		"name" => $name,
		"cookie" => $cookie,
		"note" => $note,
		"data" => $settings
	);
	_write($data);
}

/**
 * 获得指定账号的信息
 * @param  string $aid 账号 aid
 * @return array|null  账号信息
 */
function get(string $aid){
	$data = _read();
	if(!isset($data[$aid]))
		return null;
	return $data[$aid];
}

/**
 * 删除指定账号
 * @param  string $aid 账号 aid
 * @return bool      是否成功
 */
function delete(string $aid){
	$data = _read();
	if(!isset($data[$aid]))
		return false;
	unset($data[$aid]);
	_write($data);
	return true;
}

/**
 * 清空数据
 */
function clear(){
	_write(array());
}

function _write(array $data){
	global $filePath;
	file_put_contents($filePath, json_encode($data, JSON_UNESCAPED_UNICODE));
}

function _read(){
	global $filePath;
	return json_decode(file_get_contents($filePath), true); //返回为数组
}

/**
 * 设置项目根目录的相对路径
 * @param string $path 文件路径
 */
function setPath(string $path){
	global $filePath, $rootPath;
	$rootPath = $path;
	$filePath = $rootPath."/accounts.json";
}

function randomkeys(int $length){ 
	$output=''; 
	for ($a = 0; $a<$length; $a++) { 
		$output .= chr(mt_rand(65, 90)); //生成php随机数 
	} 
	return $output; 
} 