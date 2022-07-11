<?php
//@id CloudMusic
//@name 网易云音乐
//@icon https://s1.music.126.net/style/favicon.ico?v20180823
//@site music.163.com

class CloudMusic extends Runner
{
    public function run(string $aid, array &$data)
    {
        //TODO 重构 API 类
        parent::run($aid, $data);
        $this->signin($data["cookie"]);
    }

    private function signin($cookie)
    {

        $api = new API();
        //移动端
        $ret = $api->sign(0, $cookie);
        $json = json_decode($ret);
        switch ($json->code) {
    case 301:
        logError("Cookie 已失效！");
        $this->notification->append("Cookie 已失效！");
        return;
    case -2:
        logInfo("移动端重复签到");
        $this->notification->append("移动端已签到");
        break;
    case 200:
        $point = $json->point;
        logInfo("移动端已签到，云贝 +".$point);
        $this->notification->append("移动端已签到，云贝 +".$point);
        break;
    default:
        logError("未知错误代码 code=".$json->code);
        logError("返回: ".$ret);
        $this->notification->append("签到移动端时出错 code=".$json->code."，请检查运行日志！");
        break;
    }

        //pc端
        $ret = $api->sign(1, $cookie);
        $json = json_decode($ret);
        switch ($json->code) {
    case 301:
        logError("Cookie 已失效！");
        return;
    case -2:
        logInfo("PC 端重复签到");
        $this->notification->append("PC 端已签到");
        break;
    case 200:
        $point = $json->point;
        logInfo("PC 端已签到，云贝 +".$point);
        $this->notification->append("PC 端已签到，云贝 +".$point);
        break;
    default:
        logError("未知错误代码 code=".$json->code);
        logError("返回: ".$ret);
        $this->notification->append("签到 PC 端时出错 code=".$json->code."，请检查运行日志！");
        break;
    }
        $signedDays = $api->getYunbeiInfo($cookie)->data->days;
        $yunbeis = $api->getYunbeiCount($cookie);
        logInfo("已签 $signedDays 天，云贝数量 $yunbeis");
        $this->notification->append("已签 $signedDays 天，云贝数量 $yunbeis");
    }
}

//修改自：
//https://github.com/ZainCheung/netease-cloud-api
class API
{

    // General
    protected $_MINI_MODE=false;
    protected $_MODULUS='00e0b509f6259df8642dbc35662901477df22677ec152b5ff68ace615bb7b725152b3ab17a876aea8a5aa76d2e417629ec4ee341f56135fccf695280104e0312ecbda92557c93870114af6c9d05c4f7f0c3685b7a46bee255932575cce10b424d813cfe4875d3e82047b97ddef52741d546b8e289dc6935b3ece0462db0a22b8e7';
    protected $_NONCE='0CoJUm6Qyw8W8jud';
    protected $_PUBKEY='010001';
    protected $_VI='0102030405060708';
    protected $_USERAGENT='Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.157 Safari/537.36';
    protected $_COOKIE='os=pc; osver=Microsoft-Windows-10-Professional-build-10586-64bit; appver=2.0.3.131777; channel=netease; __remember_me=true;';
    protected $_REFERER='http://music.163.com/';
    // use static secretKey, without RSA algorithm
    protected $_secretKey='TA3YiYCfY2dDJQgg';
    protected $_encSecKey='84ca47bca10bad09a6b04c5c927ef077d9b9f1e37098aa3eac6ea70eb59df0aa28b691b7e75e4f1f9831754919ea784c8f74fbfadf2898b0be17849fd656060162857830e241aba44991601f137624094c114ea8d17bce815b0cd4e5b8e2fbaba978c6d1d14dc3d1faf852bdd28818031ccdaaa13a6018e1024e2aae98844210';

    // encrypt mod
    protected function prepare($raw)
    {
        $data['params']=$this->aes_encode(json_encode($raw), $this->_NONCE);
        $data['params']=$this->aes_encode($data['params'], $this->_secretKey);
        $data['encSecKey']=$this->_encSecKey;
        return $data;
    }
    protected function aes_encode($secretData, $secret)
    {
        return openssl_encrypt($secretData, 'aes-128-cbc', $secret, false, $this->_VI);
    }

    // get user detail
    public function detail($uid)
    {
        $url="https://music.163.com/weapi/v1/user/detail/${uid}";
        return newHttp($url)
                ->get()
                ->asString();
    }
    
    //签到
    public function sign(int $signType, string $cookie = "") : string
    {
        $url="https://music.163.com/weapi/point/dailyTask";
        $data=array("type" => $signType); //type=0 Android 端签到

        //伪造 IP
        //网易云貌似把腾讯云函数的 IP 拉进了黑名单，云函数测试返回 403 请稍后再试，本地测试一点问题都没有
        $rndNum = rand(0, 255);
        if(isset($_SERVER["CF"]) && $_SERVER["CF"]){
            $headerName = "X-Real-IP";
			$headerContent = "211.161.244.$rndNum";
            logInfo("检测到云函数环境，使用伪造 IP：211.161.244.$rndNum");
        }
        else{
			$headerName = "";
			$headerContent = "";
		}
        
        return newHttp($url)
                ->setCookie($cookie)
                ->addHeader($headerName, $headerContent)
                ->postForm($this->prepare($data))
                ->asString();
    }

    //云贝签到信息
    public function getYunbeiInfo(string $cookie = "")
    {
        $url = "https://music.163.com/api/point/signed/get";
        return newHttp($url)
                ->setCookie($cookie)
                ->post()
                ->asJSON();
    }

    //云贝签到（？？？）
    public function yunbeiSign($cookie = "") : string
    {
        $url = "https://music.163.com/api/point/dailyTask";
        $data = array("type" => "0");
        return newHttp($url)
                ->setCookie($cookie)
                ->postForm($this->prepare($data))
                ->asString();
    }

    //获取云贝数量
    public function getYunbeiCount($cookie) : int
    {
        $url = "https://music.163.com/api/v1/user/info";
        return newHttp($url)
                ->setCookie($cookie)
                ->post()
                ->asJSON()->userPoint->balance;
    }
}
