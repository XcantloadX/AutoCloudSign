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
    private $form;
    private $headers;
    private $url;
    private $query;

    public function __construct(string $url){
        $this->ch = curl_init();
        $this->url = $url;
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); //忽略 HTTPS 证书错误
        $this->setUA("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36");
    }

    function __destruct(){
        //防止内存泄露
        curl_close($this->ch);
    }
    
    public function setUA(string $ua) : Http{
        curl_setopt($this->ch, CURLOPT_USERAGENT, $ua); //设置 UA
        return $this;
    }

    public function addHeader(string $h) : Http{
        array_push($this->headers, $h);
        return $this;
    }
    
    public function setCookie(string $cookie) : Http{
        curl_setopt($this->ch, CURLOPT_COOKIE, $cookie);
        return $this;
    }
    
    /**
    * 设置 url 参数
    */
    public function buildQuery(array $query) : Http{
        $this->query = http_build_query($query);
        return $this;
    }

    /**
     *自动将 json 文本解码
    */
    public function asJSON() : object{
        return json_decode($this->ret);
    }

    /**
     * 获取返回结果
     */
    public function asString() : string{
        return $this->ret;
    }

    public function buildPostForm(array $form) : Http{
        $this->form = http_build_query($form);

        return $this;
    }
    
    /**
    * 发送 Get 请求
    */
    public function get() : Http{
        curl_setopt($this->ch, CURLOPT_URL, $this->url."?".$this->query);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); //返回内容储存到变量中
        curl_setopt($this->ch, CURLOPT_HEADER, $this->headers);
        $this->ret = curl_exec($this->ch);
        return $this;
    }

    /**
     * 发送 POST 请求
     * @param string $data 要 POST 的数据
     */
    public function post(string $data = null) : Http{
        curl_setopt($this->ch, CURLOPT_URL, $this->url."?".$this->query);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); //返回内容储存到变量中
        curl_setopt($this->ch, CURLOPT_POST, true); // 发送 POST 请求
        curl_setopt($this->ch, CURLOPT_HEADER, $this->headers);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        $this->ret = curl_exec($this->ch);
        return $this;
    }

    /** POST 一个表单
     * @param array $form 表单
     * @return $this 本身
     */
    public function postForm(array $form) : Http{
        return $this->post(http_build_query($form));
    }
}

?>