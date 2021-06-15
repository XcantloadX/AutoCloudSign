<?php
//@name 网易云音乐
//@site music.163.com

logSetName("CloudMusic");
logInfo("开始签到网易云音乐");
watchStart();
$nBuilder->append("网易云音乐", "---%s---","## %s");
//遍历所有 Cookie，签到
foreach($cloudmusic as $c){
	signin($c);
}

watchEnd();
logInfo("耗时 ".watchGetSec()." 秒。");
logInfo("完成");

function signin($cookie){
    $api= new API();
    $ret = $api->sign($cookie);
    $json = json_decode($ret);
    switch ($json->code) {
        case 301:
            logError("Cookie 已失效！");
            return;
        case -2:
            logInfo("重复签到");
            break;
        case 0:
            $point = $json->point;
            logInfo("已签到，点数 +".$point);
            break;
        default:
            logError("未知错误代码 code=".$json->code);
            logError("ret: ".$ret);
            break;
    }
}

//https://github.com/ZainCheung/netease-cloud-api
class API{

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
    protected function prepare($raw){
        $data['params']=$this->aes_encode(json_encode($raw),$this->_NONCE);
        $data['params']=$this->aes_encode($data['params'],$this->_secretKey);
        $data['encSecKey']=$this->_encSecKey;
        return $data;
    }
    protected function aes_encode($secretData,$secret){
        return openssl_encrypt($secretData,'aes-128-cbc',$secret,false,$this->_VI);
    }

    // CURL
    protected function curl($url,$data=null,$cookie=false){
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        if($data){
            if(is_array($data))$data=http_build_query($data);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
            curl_setopt($curl,CURLOPT_POST,1);
        }
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl,CURLOPT_REFERER,$this->_REFERER);
        curl_setopt($curl,CURLOPT_COOKIE,$this->_COOKIE."__csrf=".$_COOKIE["__csrf"]."; MUSIC_U=".$_COOKIE["MUSIC_U"]);
        curl_setopt($curl,CURLOPT_USERAGENT,$this->_USERAGENT);
        if($cookie==true){
        curl_setopt($curl,CURLOPT_HEADER,1);
        $result=curl_exec($curl);
        preg_match_all('/\{(.*)\}/', $result, $json);
        if(json_decode($json[0][0],1)["code"]==200){
            preg_match_all('/Set-Cookie: MUSIC_U=(.*?)\;/', $result, $musicu);
            preg_match_all('/Set-Cookie: __csrf=(.*?)\;/', $result, $csrf);
            setcookie("MUSIC_U",$musicu[1][0]);
            setcookie("__csrf",$csrf[1][0]);
        }
        $result = $json[0][0];
        }else{
        $result=curl_exec($curl);}
        curl_close($curl);
        return $result;
    }

    // login by phone
    public function login($cell,$pwd,$countrycode){
        $url="https://music.163.com/weapi/login/cellphone";
        $data=array(
        "phone"=>$cell,
        "countrycode"=>"86",
        "countrycode"=>$countrycode,
        "password"=>$pwd,
        "rememberLogin"=>"true");
        return $this->curl($url,$this->prepare($data),true);
    }
    // login by email
    public function loginByEmail($cell,$pwd){
        $url="https://music.163.com/weapi/login";
        $data=array(
        "username"=>$cell,
        "password"=>$pwd,
        "rememberLogin"=>"true");
        return $this->curl($url,$this->prepare($data),true);
    }
    // get user detail
    public function detail($uid){
        $url="https://music.163.com/weapi/v1/user/detail/${uid}";
        //return $this->curl($url,$this->prepare($data),true);
        return newHttp($url)
                ->get()
                ->asString();
    }
    
    public function sign($cookie = ""){
        $url="https://music.163.com/weapi/point/dailyTask";
        $data=array("type"=>0);
        //return $this->curl($url,$this->prepare($data),true);
        return newHttp($url)
                ->setCookie($cookie)
                ->postForm($this->prepare($data))
                ->asString();
    }
}

?>