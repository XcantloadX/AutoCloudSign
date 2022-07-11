<?php
//@id           NeteaseCG
//@name         网易云游戏
//@site         cg.163.com
//@description  

class NeteaseCG extends Runner{
    public function run(string $aid, array &$data){
        parent::run($aid, $data);
        $cookie = $data["cookie"];

        $response = $this->session->post(
            "https://n.cg.163.com/api/v2/sign-today",
            array(
                'authority' => 'n.cg.163.com',
                'accept' => 'application/json, text/plain, */*',
                'accept-language' => 'zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'authorization' => "$cookie",
                'cache-control' => 'no-cache',
                'content-length' => '0',
                'origin' => 'https://cg.163.com',
                'pragma' => 'no-cache',
                'referer' => 'https://cg.163.com/',
                'sec-ch-ua' => '" Not;A Brand";v="99", "Microsoft Edge";v="103", "Chromium";v="103"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"Windows"',
                'sec-fetch-dest' => 'empty',
                'sec-fetch-mode' => 'cors',
                'sec-fetch-site' => 'same-site',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.66 Safari/537.36 Edg/103.0.1264.44',
                'x-platform' => '0',
                "cookie" => ""
            )
        );
        $ret = json_decode($response->body);
        $status = $response->status_code;
        //错误检查
        //TODO 输出信息没有加入通知
        //TODO 更好的 log 与通知合一的系统

        if($status == 401){
            logError("登录信息可能已失效，请检查。");
            logError($response->body);
            return;
        }
        else if($status == 400){
            logInfo("今天已经签到过了。");
        	$this->notification->append("今天已经签到过了。");
        	return;
        }
        else if($status != 200){
            logInfo("未知错误 status={$status}。");
            logInfo("已终止");
        	$this->notification->append("未知错误 status={$status}。");
        	$this->notification->append("已终止");
        	return;
        }

        //什么？你问我返回信息是什么意思？
        //你自己去搞 js 逆向啊！
        //能用就行，反正签到信息只是锦上添花的功能
        logInfo("签到成功");
        $this->notification->append("签到成功");
    }
}
