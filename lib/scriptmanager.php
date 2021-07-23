<?php
//脚本管理器
//用于管理 script 下的脚本
namespace ScriptManager;
$path = null;

/**
 * 获取所有脚本的信息
 * @param  string $path script 目录路径
 * @return array         ID => 属性
 */
function getAll(){
	global $path;
	$files = scandir($path);
	foreach($files as $file)
	{
		if($file != "." && $file != ".." && $file != "base.php" && strpos($file, ".php") > 0){ //过滤非 php 文件
			$attr = getAttributes_($path."/".$file);
			$scripts[$attr["id"]] = $attr;

			//TODO 检查脚本是否缺少必要属性 id name
		}
	}

	return $scripts;
}

/**
 * 	禁用某个脚本
 * @param  string $id 脚本 id
 * @param  string $filepath 脚本路径
 * @return bool     是否操作成功
 */
function disable(string $id){
	global $path;
	if(file_exists($path."/".$id.".php")){
		rename($path."/".$id.".php", $path."/".$id.".disabled");
		return true;
	}
	return false;
}

/**
 * 	启用某个脚本
 * @param  string $id 脚本 id
 * @param  string $filepath 脚本路径
 * @return bool     是否操作成功
 */
function enable(string $id){
	global $path;
	if(file_exists($path."/".$id.".disabled")){
		rename($path."/".$id.".disabled", $path."/".$id.".php");
		return true;
	}
	return false;
}

/**
 * 获取指定脚本的属性信息
 * @param  string $filepath 脚本路径
 * @return array           属性名 => 属性值
 */
function getAttributes_(string $filepath){
	$attr = array();
	$f = fopen($filepath, "r");
	fgets($f); //跳过第一行 <?php
	//循环获取所有行
	while(!feof($f)){
		$line = fgets($f);
		if(substr($line, 0, strlen("//@")) == "//@"){
			//获取属性
			$line = str_replace("//@", "", $line);
			$arr = explode(" ", $line);
			if(count($arr) < 2){
				//throw TODO....
			}
			$attr[$arr[0]] = trim($arr[1]); //trim 删除末尾 \n
		}
		else{
			break;
		}
	}

	//判断是否已启用
	if(strpos($filepath, ".php") > 0)
		$attr["enabled"] = true;
	else
		$attr["enabled"] = false;
	return $attr;
}

/**
 * 获取指定 ID 的脚本的属性信息
 * @param  string $id   ID
 * @param  string $path script 目录路径
 * @return array       属性名 => 属性值
 */
function getAttributes(string $id, string $path){
	global $path;
	return getAttributes_($path."/".strtolower($id).".php");
}

function setPath(string $scriptPath){
	global $path;
	$path = $scriptPath;
}