<?php
//@id TieBa
//@name 百度贴吧
//@icon https://www.baidu.com/favicon.ico
//@site tieba.baidu.com

class TieBa extends Runner
{
    public function run(string $aid, array $data)
    {
        $this->signAll($data["cookie"]);
    }
    
    /** 签到单个贴吧
 	* @param string $name 吧名
 	* @param string $cookie Cookie
 	* @param string|null $captchaVcode 验证码 vcode（如果不需要验证留空）
 	* @param string|null $captchaInput 验证码对应字符（如果不需要验证留空）
 	* @return bool|string 签到结果（json 字符串）
 	*/
    private function sign(string $name, string $cookie, string $captchaVcode = null, string $captchaInput = null)
    {
        if (isset($captchaVcode) && $captchaVcode != null) {
            $data = ["ie" => "utf-8", "kw" => $name, "captcha_vcode_str" => $captchaVcode, "captcha_input_str" => $captchaInput];
        } else {
            $data = ["ie" => "utf-8", "kw" => $name];
        }
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://tieba.baidu.com/sign/add");
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36"); //设置 UA
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true); // 发送 Post 请求
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //请求参数
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //返回内容储存到变量中
    
    	$json = curl_exec($ch);
    
        return $json;
    }


    /**
     * 签到所有贴吧
     * @param string $cookie 账号的 Cookie
     * @throws Exception
     */
    private function signAll(string $cookie)
    {
        $errMsg = null;
        $names = $this->getAllBars($cookie);
        $signed = 0; //签到成功个数
    
        //循环签到所有贴吧
        for ($i = 0; $i < count($names); $i++) {
            $interval = 0.2 * 100000;
            $retryCount = 0;
            $maxRetryCount = 3;
            $captchaInput = null;
            $captchaVcode = null;

        startSign: //-------goto 到这里-------
        	if ($retryCount >= $maxRetryCount) {
            	logError("签到贴吧 ".$names[$i]."吧 时重试次数过多！签到终止！");
            	$this->notification->append("签到贴吧 ".$names[$i]."吧 时重试次数过多！签到终止！");
            	break;
        	}

            $json = $this->sign($names[$i], $cookie, $captchaInput, $captchaVcode);
            $json = json_decode($json);
        
            //错误码处理
            $code = intval($json->no);
            switch ($code) {
            case 0: //一切正常
            case 1101: //今天已签到过
                $retryCount = 0;
                $captchaInput = null;
                $captchaVcode = null;
                $signed++;
                logInfo($names[$i]."吧 已签到");
				break;
				
            case 1011: //您还未加入此吧或等级不够
                logWarn("尚未关注 ".$names[$i]."，无法进行签到");
				continue;
				
            case 1990055: //未登录
                logError("Cookie 已失效，请重新设置！");
                logError("返回 json：".json_encode($json));
                $errMsg = "Cookie 已失效，请重新设置！";
				break;
				
            case 1102: //您签得太快了 ，先看看贴子再来签吧:)
                logError("签到频率过快！1s 后重试...($retryCount/$maxRetryCount)");
                sleep(1);
                $retryCount++;
				goto startSign;
				
            case 2150040: //需要验证码
                logWarn("需要验证码！正在尝试验证...");
                //这个验证码随便输点字符就能通过（意义何在？？？）
                $randomInput = bin2hex(random_bytes(2)); //产生四位随机数字/字母
                $ret = newHttp("https://tieba.baidu.com/sign/checkVcode")
                    ->setCookie($cookie)
                    ->postForm(array("captcha_vcode_str" => $json->data->captcha_vcode_str, "captcha_input_str" => $randomInput))
                    ->asJSON();
                if ($ret->anti_valve_err_no != 0) {
                    logError("验证失败！终止。");
                    $errMsg = "签到 ".$names[$i]."吧 时验证码验证失败。";
                    break;
                } else {
                    //logInfo("验证成功，正在重试...");
                    $captchaVcode = $json->data->captcha_vcode_str;
                    $captchaInput = $randomInput;
                    goto startSign;
                }
                // no break
            default:
                logError("未知错误码 ".$code);
                logError("返回 json: ".json_encode($json));
                logInfo("1s 后重试...($retryCount/$maxRetryCount)");
                sleep(1);
                $retryCount++;
                goto startSign;
        }
            usleep($interval);
        }

        $t2 = microtime(true);
        logInfo("已成功签到：".$signed."/".count($names)." 个贴吧。");
        if ($errMsg != null) {
            $this->notification->append("错误：".$errMsg);
        }
        $this->notification->append("已成功签到：".$signed."/".count($names)." 个贴吧。");
    }

    //获取所有关注的贴吧的名称
    private function getAllBars(string $cookie) : array
    {
        //带 Cookie 获取贴吧首页源码
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://tieba.baidu.com");
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36"); //设置 UA
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $html = curl_exec($ch);

        //解析
        $start = strpos($html, "spage/widget/forumDirectory") + 27 + 2;
        $end = strpos($html, "</script>", $start) - 2;
        $json = substr($html, $start, $end - $start);

        $json = json_decode($json);
        $names = array();
    
        //遍历出所有名称
        for ($i = 0; $i < count($json->forums); $i++) {
            array_push($names, $json->forums[$i]->forum_name);
        }
    
        return $names;
    }
}