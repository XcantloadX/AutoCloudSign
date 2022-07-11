<?php
//@id Bilibili
//@name 哔哩哔哩
//@icon https://www.bilibili.com/favicon.ico?v=1
//@site bilibili.com

class Bilibili extends Runner
{
    public function run(string $aid, array &$data)
    {
        parent::run($aid, $data);
        $this->signin();
    }

    /**
    * 签到
    * @param cookie 账号的 Cookie
    */
    private function signin()
    {
        $signedDays = 0;
        $allDays = 0;

        //---主站---
        //带 Cookie 访问该 API 就可以签到
        $ret = $this->session->get("http://api.bilibili.com/x/web-interface/nav")->body;
        $json = json_decode($ret);

        //错误检查
        if ($json->code == -101) {
            $this->notification->append("Cookie 已失效！请重新设置。");
            logError("Cookie 已失效！请重新设置。");
            return;
        } elseif ($json->code != 0) {
            logError("未知错误！code=".$json->code);
            logError("返回 json: ".$ret);
            $this->notification->append("未知错误！code=".$json->code);
            $this->notification->append("返回 json: ".$ret, "%s", "返回 json:".PHP_EOL."```".PHP_EOL.$ret.PHP_EOL."```");
        }

        $name = $json->data->uname;
        $this->notification->append("@".$name, "%s", "### %s");


        $this->notification->append("硬币：".$this->getCoinNum());
        logInfo("账号 @".$name." 主站签到成功。"."硬币数量：".$this->getCoinNum());
    
        //---直播---
        $ret = null;
        $ret = $this->session->get("https://api.live.bilibili.com/xlive/web-ucenter/v1/sign/DoSign")->body;
        $json = json_decode($ret);

        if ($json->code == 1011040) {
            logInfo("账号 @".$name." 直播站今天已经签到过了");
        } elseif ($json->code != 0) {
            logInfo("账号 @".$name." 直播站签到失败：".$json->message);
            $this->notification->append("直播站签到失败".$json->message);
        } else {
            logInfo("账号 @".$name." 直播站签到成功。获得 ".$json->data->text." ，本月已签 ".$json->data->hadSignDays."/".$json->data->allDays." 天");
            $signedDays = $json->data->hadSignDays;
            $allDays = $json->data->allDays;
        }


        //---漫画---
        //签到得积分。注意积分年末清零！
        $ret = null;

        $ret = $this->session->post("https://manga.bilibili.com/twirp/activity.v1.Activity/ClockIn", array(),"platform=android")->body;
        $json = json_decode($ret);

        if ($json->msg == "clockin clockin is duplicate") {
            logInfo("账号 @".$name." 漫画站今天已经签到过了");
        } elseif ($json->code == 0) {
            logInfo("账号 @".$name." 漫画站签到成功");
        } else {
            logInfo("账号 @".$name." 漫画站签到失败：".$json->msg);
            $this->notification->append("漫画站签到失败：".$json->msg);
        }

        $this->notification->append("本月已签 ".$signedDays."/".$allDays." 天");
    }


    //获取硬币数量
    private function getCoinNum()
    {
        $json = $this->session->get("http://account.bilibili.com/site/getCoin")->body;
        $json = json_decode($json);
        return $json->data->money;
    }
}
