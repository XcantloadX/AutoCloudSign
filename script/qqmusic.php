<?php
//@id QQMusic
//@name QQ 音乐
//@icon 
//@site y.qq.com

class QQMusic extends Runner {
    public function run(string $aid, array &$data) {
        parent::run($aid, $data);
        //https://stackoverflow.com/questions/30714638/how-can-i-parse-serverhttp-cookie-in-pure-php
        parse_str(strtr($data["cookie"], array('&' => '%26', '+' => '%2B', ';' => '&')), $cookies);
        $uin = str_replace("o","", $cookies["uin"]); //QQ 号
        $uid = isset($cookies["uid"]) ? $cookies["uid"] : ""; //uid，貌似不要也行

        //TODO 微信登录的账号未测试，貌似是分开的
        //以下是请求体
        $json = 
<<<DATA
{
    "comm": {
        "g_tk": 1025738002,
        "uin": $uin,
        "format": "json",
        "inCharset": "utf-8",
        "outCharset": "utf-8",
        "notice": 0,
        "platform": "h5",
        "needNewCode": 1,
        "ct": 23,
        "cv": 0,
        "uid": "$uid"
    },
    "req_0": {
        "module": "music.actCenter.ActCenterSignNewSvr",
        "method": "DoSignIn",
        "param": {
            "ActID": "PR-Config20200828-31525466015"
        }
    }
}
DATA;

        $t = time() * 1000;
        $sign = $this->makeSign($json);
        $response = $this->session->post(
            "https://u.y.qq.com/cgi-bin/musics.fcg?_webcgikey=DoSignIn&_=$t&sign=$sign",
            array(
                'Host' => 'u.y.qq.com',
                'Connection' => 'keep-alive',
                'Accept' => 'application/json',
                'Origin' => 'https://i.y.qq.com',
                'User-Agent' => 'Mozilla/5.0 (Linux; Android 5.1.1; VOG-AL00 Build/LMY48Z; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/52.0.2743.100 Mobile Safari/537.36 QQJSSDK/1.3 /ANDROIDQQMUSIC/11050108 QQMusic/11.5.1.8 H5/1 NetType/WIFI Mskin/white isNorch/0 topBar/104 topBarShrink/64 Pixel/720 Mcolor/3ce68eff  Bcolor/0  skinid[902] skin_css/skin2_1_902 channel/70312 DeviceLevel/50 DeviceScore/507.0 H5TestFPS/0',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Referer' => 'https://i.y.qq.com/n2/m/client/actcenter/index.html?_hdct=2',
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => 'zh-CN,en-US;q=0.8',
                'X-Requested-With' => 'com.tencent.qqmusic'
            ),
            $json
        );
        $ret = json_decode($response->body);
        //检查错误
        if($ret->code != 0){
            logError("未知错误");
            logError("返回信息：".$response->body);
            return;
        }
        else if($ret->req_0->code == 1000){
            logError("Cookie 已失效，若此错误持续，请提交 issue。");
            logError("返回信息：".$response->body);
            return;
        }
        else if($ret->req_0->code == 2000){
            logError("sign 参数，可能为脚本错误，请提交 issue。");
            logError("返回信息：".$response->body);
            return;
        }
        else if($ret->req_0->code == 200002){
            $weekSignCount = $ret->req_0->data->WeekSignCount;
            $monthSignCount = $ret->req_0->data->MonthSignCount;
            $yearSignCount = $ret->req_0->data->YearSignCount;
            logInfo("今天已签到。");
            logInfo("本周已签 {$weekSignCount} 天，本月已签 {$monthSignCount} 天，今年已签 {$yearSignCount} 天。");
            return;
        }
        $this->notification->append("账号 @".$data["name"]." 签到成功", "%s", "### %s");
        //TODO 自动领取签到奖励
    }

    /** 生成 sign
     * @param string $param 要提交的参数
     * @return string 生成的 sign
     * @throws Exception 由 bin2hex() 抛出
     */
    function makeSign(string $param) : string{
        //https://blog.csdn.net/qq_23594799/article/details/111477320
        //sign = "zza" + 随机十位字符 + md5($param);
        return "zza".bin2hex(random_bytes(5)).md5("CJBPACrRuNy7".$param);
    }
}