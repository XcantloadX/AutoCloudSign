<?php
$startTime = 0;
$endTime = 0;

//开始计时
function watchStart()
{
	global $startTime;
	$startTime = microtime(true);
}

//结束计时
function watchEnd()
{
	global $endTime;
	$endTime = microtime(true);
}

//获取时间
function watchGetSec()
{
	global $startTime, $endTime;
	return round($endTime - $startTime, 3);
}