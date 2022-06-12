<?php
use WpOrg\Requests\Requests;
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
    private $ret;
    private $form;
    private $headers;
    private $url;
    private $query;

    public function __construct(string $url){
        $this->url = $url;
        $this->headers = array();
        $this->query = null;
        $this->setUA("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36");
    }

    function __destruct(){
    }
    
    public function setUA(string $ua) : Http{
        $this->headers["User-Agent"] = $ua;
        return $this;
    }

    public function addHeader(string $h) : Http{
        array_push($this->headers, $h);
        return $this;
    }
    
    public function setCookie(string $cookie) : Http{
        $this->headers["Cookie"] = $cookie;
        return $this;
    }
    
    /**
    * 设置 url 参数。可选函数，若不调用，也可以自行在 url 添加参数。
    */
    public function buildQuery(array $query) : Http{
        $this->query = http_build_query($query);
        return $this;
    }

    /**
     *获取响应并自动解码 json
     * @return object 响应
    */
    public function asJSON(){
        return json_decode($this->ret);
    }

    /**
     * 获取响应
     * @return string 响应
     */
    public function asString() : string{
        return $this->ret;
    }

    /**
     * 以 String 格式返回响应，并截取指定部分。若未找到则返回空字符串。
     * @param  string $startStr 开始的字符串（不包括此部分），特殊字符需要转义
     * @param  string $endStr   结束的字符串（不包括此部分），特殊字符需要转义
     * @return string        截取结果
     */
    public function asStringBetween(string $startStr, string $endStr) : string{
        $response = $this->asString();
        preg_match("/$startStr(.*?)$endStr/", $response, $matches);
        if(count($matches) <= 0)
            return "";
        else
            return $matches[1];
    }

    public function buildPostForm(array $form) : Http{
        $this->form = http_build_query($form);

        return $this;
    }
    
    /**
    * 发送 Get 请求
    */
    public function get() : Http{
		$url = $this->url;
        if($this->query != null)
            $url = $this->url."?".$this->query;
        $this->ret = Requests::get($url, $this->headers, array('verify' => false))->body;
        return $this;
    }

    /**
     * 发送 POST 请求
     * @param string $data 要 POST 的数据
     */
    public function post(string $data = null) : Http{
		$url = $this->url;
        if($this->query != null)
            $url = $this->url."?".$this->query;
		
         $this->ret = Requests::post($url, $this->headers, $data, array('verify' => false))->body;
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