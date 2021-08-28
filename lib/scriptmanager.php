<?php
//脚本管理器
//用于管理 script 下的脚本
namespace ScriptManager;

$rootPath = "";
$filePath = "";

/**
 * 获取所有脚本的信息
 * @return array         ID => 属性
 * @throws ScriptException
 */
function getAll() : array{
	global $filePath;
	$scripts = array();
	$files = scandir($filePath);
	foreach($files as $file)
	{
		if($file != "." && $file != ".." && $file != "base.php" && strpos($file, ".php") > 0){ //过滤非 php 文件
			$attr = getAttributes_($filePath."/".$file);
			$scripts[$attr["id"]] = $attr;
		}
	}

	return $scripts;
}

/**
 * 	禁用某个脚本
 * @param  string $id 脚本 id
 * @return bool     是否操作成功
 */
function disable(string $id) : bool{
	global $filePath;
	if(file_exists($filePath."/".$id.".php")){
		rename($filePath."/".$id.".php", $filePath."/".$id.".disabled");
		return true;
	}
	return false;
}

/**
 * 	启用某个脚本
 * @param  string $id 脚本 id
 * @return bool     是否操作成功
 */
function enable(string $id) : bool{
	global $filePath;
	if(file_exists($filePath."/".$id.".disabled")){
		rename($filePath."/".$id.".disabled", $filePath."/".$id.".php");
		return true;
	}
	return false;
}

/**
 * 获取指定脚本的属性信息
 * @param string $filepath 脚本路径
 * @return array           属性名 => 属性值
 * @throws ScriptException
 */
function getAttributes_(string $filepath) : array{
	$attr = array();
	$f = fopen($filepath, "r");
	fgets($f); //跳过第一行 <?php
	//循环获取所有行
	while(!feof($f)){
		$line = fgets($f);
		preg_match("/\/\/@(.*?)\s+(.*)/", $line, $matches);
		if(count($matches) >= 3){
			$attr[$matches[1]] = trim($matches[2]);
		}
		else{
			break;
		}
	}

    if(!isset($attr["name"]))
        throw new ScriptException("关键属性 name 缺失。", $filepath);
    if(!isset($attr["id"]))
        throw new ScriptException("关键属性 id 缺失。", $filepath);

	//判断是否已启用
	if(strpos($filepath, ".php") > 0)
		$attr["enabled"] = true;
	else
		$attr["enabled"] = false;
	return $attr;
}

/**
 * 获取指定 ID 的脚本的属性信息
 * @param string $id ID
 * @param string $path script 目录路径
 * @return array       属性名 => 属性值
 * @throws ScriptException
 */
function getAttributes(string $id, string $path) : array{
	global $path;
	return getAttributes_($path."/".strtolower($id).".php");
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

/**
 * 脚本异常类
 */
class ScriptException extends \Exception {
    private $name;
    public function __construct($message = "", $scriptName = "") {
        parent::__construct($message);
        $this->name = $scriptName;
    }

    public function __toString() {
        return "脚本 $this->name 出现错误：$this->message";
    }
}