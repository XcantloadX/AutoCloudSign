<?php
//@name 哔哩哔哩


logSetName("Bilibili");
logInfo("开始签到哔哩哔哩");
watchStart();

//遍历所有 Cookie，签到
foreach($bilibili as $b)
{
	signin($b);
}

watchEnd();
logInfo("耗时 ".watchGetSec()." 秒。");
logInfo("完成");

/*
 * 签到
 * @param cookie 账号的 Cookie
 */
function signin($cookie){
    //主站
	//带 Cookie 访问该 API 就可以签到
	$ret = hGet(array(
		"url" => "http://api.bilibili.com/x/web-interface/nav",
		"cookie" => $cookie
	));
	$json = json_decode($ret);
    $name = $json->data->uname;
	
    if($json->code == -101){
        logError("Cookie 已失效！请重新设置。");
    }
	else if($json->code != 0){
		logError("出现错误！code=".$json->code);
		logError("返回 json: ".$ret);
        return;
	}

	logInfo("账号 @".$name." 主站签到成功。"."硬币数量：".getCoinNum($cookie));
    
    //直播
    $ret = null;
    $ret = newHttp("https://api.live.bilibili.com/xlive/web-ucenter/v1/sign/DoSign")
        ->setCookie($cookie)
        ->get()
        ->asJSON();
    if($ret->code == 1011040){
        logInfo("账号 @".$name." 直播站今天已经签到过了");
    }
    else if($ret->code != 0)
        logInfo("账号 @".$name." 直播站签到失败：".$ret->message);
    else
        logInfo("账号 @".$name." 直播站签到成功。获得 ".$ret->data->text." ，本月已签 ".$ret->data->hadSignDays."/".$ret->data->allDays." 天");
    
    //漫画
    //签到得积分。注意积分年末清零！
    $ret = null;
    $ret = newHttp("https://manga.bilibili.com/twirp/activity.v1.Activity/ClockIn")
        ->setCookie($cookie)
        ->post("platform=android")
        ->asJSON();

    if($ret->msg == "clockin clockin is duplicate")
        logInfo("账号 @".$name." 漫画站今天已经签到过了");
    else if($ret->code == 0)
        logInfo("账号 @".$name." 漫画站签到成功");
    else
        logInfo("账号 @".$name." 漫画站签到失败：".$ret->msg);
}


//获取硬币数量
function getCoinNum($cookie){
	$json = hGet(array(
		"url" => "http://account.bilibili.com/site/getCoin",
		"cookie" => $cookie
	));
	
	$json = json_decode($json);
	return $json->data->money;
}

function biliLiveSignIn($cookie){

    
}