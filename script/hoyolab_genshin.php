<?php
//@id Hoyolab_Genshin
//@name 原神国际服
//@icon https://genshin.hoyoverse.com/favicon.ico
//@site genshin.hoyoverse.com
//@note 

class Hoyolab_Genshin extends Runner {
    public function run(string $aid, array &$data) {
        parent::run($aid, $data);
        
        //签到
        $ret = $this->session->post(
            "https://sg-hk4e-api.hoyolab.com/event/sol/sign?lang=zh-cn",
            array(
                'authority' => 'sg-hk4e-api.hoyolab.com',
                'accept' => 'application/json, text/plain, */*',
                'accept-language' => 'zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'cache-control' => 'no-cache',
                'content-type' => 'application/json;charset=UTF-8',
                //'cookie' => '',
                'origin' => 'https://act.hoyolab.com',
                'pragma' => 'no-cache',
                'referer' => 'https://act.hoyolab.com/',
                'sec-ch-ua' => '"Chromium";v="104", " Not A;Brand";v="99", "Microsoft Edge";v="104"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"Windows"',
                'sec-fetch-dest' => 'empty',
                'sec-fetch-mode' => 'cors',
                'sec-fetch-site' => 'same-site',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.102 Safari/537.36 Edg/104.0.1293.63'
            ),
            '{"act_id":"e202102251931481"}'
        )->body;

        $json = json_decode($ret);
        $msg = "";
        switch($json->retcode){
            /* {"retcode":0,"message":"OK","data":{"code":"ok"}} */
            case 0:
                $msg = "签到成功。";
                break;

            /* {"data":null,"message":"旅行者，你已经签到过了~","retcode":-5003} */
            case -5003:
                $msg = "旅行者，你已经签到过了~";
                break;

            /* {"data":null,"message":"尚未登录","retcode":-100} */
            case -100:
                $msg = "Cookie 无效。";
                break;

            /* {"data":null,"message":"参数异常","retcode":-1005} */
            case -1005:
                $msg = "参数异常。可能为脚本 bug 或签到 API 有更改，请提交 issue。";
                break;
            default:
                break;
        }
        logInfo($msg);

        //取签到奖励
        $ret2 = $this->session->get(
            "https://sg-hk4e-api.hoyolab.com/event/sol/info?lang=zh-cn&act_id=e202102251931481",
            array(
                'authority' => 'sg-hk4e-api.hoyolab.com',
                'accept' => 'application/json, text/plain, */*',
                'accept-language' => 'zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'cache-control' => 'no-cache',
                'origin' => 'https://act.hoyolab.com',
                'pragma' => 'no-cache',
                'referer' => 'https://act.hoyolab.com/',
                'sec-ch-ua' => '"Chromium";v="104", " Not A;Brand";v="99", "Microsoft Edge";v="104"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"Windows"',
                'sec-fetch-dest' => 'empty',
                'sec-fetch-mode' => 'cors',
                'sec-fetch-site' => 'same-site',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.102 Safari/537.36 Edg/104.0.1293.63'
              )
        )->body;
        $json2 = json_decode($ret2);
        if($json2->retcode != 0){
            logError("获取签到信息失败。");
            logInfo("返回 json: ".$ret2);
            return;
        }

        $signedDays = $json2->data->total_sign_day;
        $award = $this->getAwardList()[$signedDays];
        logInfo("获得 {$award->name}x{$award->cnt}。请在游戏内领取，有效期 30 天！");
        logInfo("本月已签 {$signedDays} 天。");
    }

    public function getAwardList(){
        $ret = $this->session->get(
            "https://sg-hk4e-api.hoyolab.com/event/sol/home?lang=zh-cn&act_id=e202102251931481",
            array(
                'authority' => 'sg-hk4e-api.hoyolab.com',
                'accept' => 'application/json, text/plain, */*',
                'accept-language' => 'zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'cache-control' => 'no-cache',
                'origin' => 'https://act.hoyolab.com',
                'pragma' => 'no-cache',
                'referer' => 'https://act.hoyolab.com/',
                'sec-ch-ua' => '"Chromium";v="104", " Not A;Brand";v="99", "Microsoft Edge";v="104"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"Windows"',
                'sec-fetch-dest' => 'empty',
                'sec-fetch-mode' => 'cors',
                'sec-fetch-site' => 'same-site',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.102 Safari/537.36 Edg/104.0.1293.63'
            )
        )->body;

        $json = json_decode($ret);
        date_default_timezone_set("Asia/Shanghai");
        return $json->data->awards;
    }
}