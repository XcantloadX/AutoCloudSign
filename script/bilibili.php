<?php
//@id Bilibili
//@name 哔哩哔哩
//@site bilibili.com

class Bilibili implements Runner
{
    public function run()
    {
        global $bilibili, $nBuilder;

        if (count($bilibili) <= 0) {
            return;
        }
        
        //遍历所有 Cookie，签到
        foreach ($bilibili as $b) {
            $this->signin($b);
        }
    }

    /*
 * 签到
 * @param cookie 账号的 Cookie
 */
    private function signin($cookie)
    {
        global $nBuilder; //来自 start.php
        $signedDays = 0;
        $allDays = 0;

        //主站
        //带 Cookie 访问该 API 就可以签到
        $json = newHttp("http://api.bilibili.com/x/web-interface/nav")
            ->setCookie($cookie)
            ->get()
            ->asJSON();
        $name = $json->data->uname;
        $nBuilder->append("@".$name, "%s", "### %s");

        if ($json->code == -101) {
            $nBuilder->append("Cookie 已失效！请重新设置。");
            logError("Cookie 已失效！请重新设置。");
            return;
        } elseif ($json->code != 0) {
            logError("未知错误！code=".$json->code);
            logError("返回 json: ".$ret);
            $nBuilder->append("未知错误！code=".$json->code);
            $nBuilder->append("返回 json: ".$ret, "%s", "返回 json:".PHP_EOL."```".PHP_EOL.$ret.PHP_EOL."```");
        }
        $nBuilder->append("硬币：".$this->getCoinNum($cookie));
        logInfo("账号 @".$name." 主站签到成功。"."硬币数量：".$this->getCoinNum($cookie));
    
        //直播
        $ret = null;
        $ret = newHttp("https://api.live.bilibili.com/xlive/web-ucenter/v1/sign/DoSign")
        ->setCookie($cookie)
        ->get()
        ->asJSON();
        if ($ret->code == 1011040) {
            logInfo("账号 @".$name." 直播站今天已经签到过了");
        } elseif ($ret->code != 0) {
            logInfo("账号 @".$name." 直播站签到失败：".$ret->message);
            $nBuilder->append("直播站签到失败".$ret->message);
        } else {
            logInfo("账号 @".$name." 直播站签到成功。获得 ".$ret->data->text." ，本月已签 ".$ret->data->hadSignDays."/".$ret->data->allDays." 天");
            $signedDays = $ret->data->hadSignDays;
            $allDays = $ret->data->allDays;
        }


        //漫画
        //签到得积分。注意积分年末清零！
        $ret = null;
        $ret = newHttp("https://manga.bilibili.com/twirp/activity.v1.Activity/ClockIn")
        ->setCookie($cookie)
        ->post("platform=android")
        ->asJSON();

        if ($ret->msg == "clockin clockin is duplicate") {
            logInfo("账号 @".$name." 漫画站今天已经签到过了");
        } elseif ($ret->code == 0) {
            logInfo("账号 @".$name." 漫画站签到成功");
        } else {
            logInfo("账号 @".$name." 漫画站签到失败：".$ret->msg);
            $nBuilder->append("漫画站签到失败：".$ret->msg);
        }

        $nBuilder->append("本月已签 ".$signedDays."/".$allDays." 天");
    }


    //获取硬币数量
    private function getCoinNum($cookie)
    {
        $json = hGet(array(
        "url" => "http://account.bilibili.com/site/getCoin",
        "cookie" => $cookie
    ));
    
        $json = json_decode($json);
        return $json->data->money;
    }
}
