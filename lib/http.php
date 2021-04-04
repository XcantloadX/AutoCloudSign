<?php
function http($params)
{
	if(!isset($params["url"]))
		return null;
	$url = $params["url"];
	$method = isset($params["method"]) ? $params["method"] : "GET";
	$cookie = isset($params["cookie"]) ? $params["cookie"] : "";
	$ua = isset($params["ua"]) ? $params["ua"] : "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36";
	$header = isset($params["header"]) ? $params["header"] : array();
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_USERAGENT, $ua); //设置 UA
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //忽略 HTTPS 证书错误
	if($method == "POST")
		curl_setopt($ch, CURLOPT_POST, true); // 发送 Post 请求
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //请求参数
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //返回内容储存到变量中
	
	return curl_exec($ch);
}

function hGet($params)
{
	$parmas["method"] = "GET";
	return http($params);
}

function hPost($params)
{
	$parmas["method"] = "POST";
	return http($params);
}

function newHttp(string $url){
    return new Http($url);
}

class Http{
    private $ch;
    private $ret;
    
    function __construct(string $url){
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); //忽略 HTTPS 证书错误
        $this->setUA("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36");
    }
    
    function setUA(string $ua){
        curl_setopt($this->ch, CURLOPT_USERAGENT, $ua); //设置 UA
        return $this;
    }
    
    
    function setCookie(string $cookie){
        curl_setopt($this->ch, CURLOPT_COOKIE, $cookie);
        return $this;
    }
    
    /*
     *自动将 json 文本解码
    */
    function asJSON(){
        return json_decode($this->ret);
    }
    
    function asString(){
        return $ret;
    }
    
    /*
    * 发送 Get 请求
    */
    function get(){
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); //返回内容储存到变量中
        $this->ret = curl_exec($this->ch);
        return $this;
    }
    
    function post(string $data){
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); //返回内容储存到变量中
        curl_setopt($this->ch, CURLOPT_POST, true); // 发送 POST 请求
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        $this->ret = curl_exec($this->ch);
        return $this;
    }
}

?>