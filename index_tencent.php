<?php

function main_handler($event, $context) {
    $GLOBALS["SERVERLESS"] = true;
    //API 请求
    if(isset($event->requestContext)){
        //获取请求头
        $headers = $event->headers;
        //处理请求头
        $headers = $event->headers;
        foreach ($headers as $k => $v) {
            $k = "HTTP_".str_replace("-", "_", strtoupper($k));
            $_SERVER[$k] = $v;
        }
        $_SERVER["REQUEST_METHOD"] = $event->requestContext->httpMethod;

        //处理 URL
        $arr = explode("?", $event->path);
        $path = $arr[0];
        $path = substr($path, strpos($path, "/",  1)); //截掉云函数名
        //处理 GET 参数
        if(count($arr) >= 2){
            $query = $arr[1];
            $params = explode("&", $query);
            foreach ($params as $param) {
                $kv = explode("=", $param);
                $_GET[$kv[0]] = $kv[1];
            }
        }
        else
            $query = "";
        $_SERVER["QUERY_STRING"] = $query;

        //处理 POST 参数
        //TODO

        //解析要运行的脚本
        $path = ".".$path; // ./xxx/xxx
        if($path[strlen($path) - 1] == "/"){
            if(file_exists($path."index.php"))
                $path = $path."index.php";
            elseif(file_exists($path."index.html"))
                $path = $path."index.php";
            elseif(file_exists($path."index.htm"))
                $path = $path."index.htm";
        }
    }
    //定时任务
    elseif (isset($event->Type) && $event->Type == "Timer") {
        $path = "./start.php";
    }

    //捕捉标准输出
    ob_start();
    include $path;
    $content = ob_get_clean();
    $arr = array(
        "isBase64Encoded" => false,
        "statusCode" => 200,
        "headers" => array("Content-Type"=>"text/html; charset=utf-8"),
        "body"=> $content
    );
    return $arr;
}