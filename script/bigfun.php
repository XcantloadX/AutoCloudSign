<?php
//@id BigFun
//@name 毕方社区
//@site bigfun.cn
//@icon https://www.bigfun.cn/favicon.ico

class BigFun extends Runner {
    public function run(string $aid, array $data) {
        $this->signin($data["cookie"]);

    }

    private function signin(string $cookie){
    	//签到
    	$crsf = $this->getCRSF($cookie);
    	$ret = newHttp("https://www.bigfun.cn/api/client/web?method=checkIn")
    			->setCookie($cookie)
    			->addHeader("x-csrf-token: ".$crsf)
    			->post()
    			->asJSON();
    	if(isset($ret->errors) && $ret->errors->code == 403){
    	    logError("Cookie 已失效！");
            $this->notification->append("Cookie 已失效！");
    	    return;
        }
    	//获取用户信息
    	$info = $this->getUserInfo($cookie);
    	$name = $info->data[0]->nickname;
    	$level = $info->data[0]->level;
    	$countinuedSignInDays = $info->data[0]->continued_check_in_days;
    	$signInDays = $info->data[0]->check_in_days;
    	$upgradeExp = $info->data[0]->upgrade_exp;
    	$exp = $info->data[0]->current_exp;

    	//输出信息
    	$this->notification->append("@".$name, "%s", "### %s");
    	if($ret->data[0]->msg == ""){
    		logInfo("账号 @$name 今天已签到");
            $this->notification->append("账号 @$name 今天已签到");
    	}
    	else{
    		logInfo("账号 @$name 签到成功，".$ret->data[0]->msg."，目前 LV.$level ，升级还需 $exp/$upgradeExp");
            $this->notification->append("账号 @$name 签到成功，".$ret->data[0]->msg."，目前 LV.$level ，升级还需 $exp/$upgradeExp");
    	}
    }

    private function getCRSF(string $cookie){
    	$crsf = newHttp("https://www.bigfun.cn")
    		->setCookie($cookie)
    		->get()
    		->asStringBetween('\<meta name="csrf-token" content="', '"\/\>');
    	return $crsf;
    }

    private function getUserInfo(string $cookie){
    	$crsf = $this->getCRSF($cookie);
    	return newHttp("https://www.bigfun.cn/api/client/web?method=getUserProfile")
    			->setCookie($cookie)
    			->addHeader("x-csrf-token: ".$crsf)
    			->get()
    			->asJSON();
    }

    public function getName(string $cookie){
    	return $this->getUserInfo($cookie)->data[0]->nickname;
    }
}