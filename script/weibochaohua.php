<?php
//@id WeiBoChaoHua
//@name 微博超话
//@icon https://weibo.com/favicon.ico
//@site weibo.com/u/

class WeiBoChaoHua extends Runner {
    public function run(string $aid, array $data) {
        $cookie = $data["cookie"];
		//获取所有关注的超话
		$list = $this->getChaoHuaList($cookie);
		if($list == null)
			return;
		$count = 0; //签到成功个数
		$countAll = count($list);//总个数

		foreach($list as $chaohua){
			$id = substr($chaohua->oid, 5);
			$name = $chaohua->title;
			$rnd = mt_rand(1000000000000, 9999999999999);
			$url = "https://weibo.com/p/aj/general/button?ajwvr=6&api=http://i.huati.weibo.com/aj/super/checkin&texta=签到&textb=已签到&status=0&id=$id&location=page_100808_super_index&timezone=GMT 0800&lang=zh-cn&plat=Win32&ua=Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36&screen=1600*900&__rnd=$rnd";
			$json = newHttp($url)
				->setCookie($cookie)
				->get()
				->asJSON();
			/*返回码
				100000：成功
				382004：重复签到
				382010：超话不存在
			*/
			$code = $json->code;

			$msg = $json->msg; 
			//签到成功
			if($code == 100000){
				$msg2 = $json->data->alert_title." ".$json->data->alert_subtitle;
				logInfo("请求签到超话 $name （id=$id ）成功，返回信息：$msg。");
				$count++;
			}
			//重复
			else if($code == 382004){
				logInfo("请求签到超话 $name （id=$id ）成功，重复签到。");
				$count++;
			}
			//失败
			else{
				$msg2 = "已签到";
				logInfo("请求签到超话 $name （id=$id ）失败，返回信息：$msg。");
			}
        	
        	
		}
		//TODO 增加昵称查询功能
		//https://weibo.com/ajax/profile/info?uid=xxxxxx
		if($count >= $countAll)
        	$this->notification->append("账号 @".$data["name"]." 超话签到成功。");
		else if($count < $countAll)
        	$this->notification->append("账号 @".$data["name"]." 超话签到异常，成功 $count/$countAll 。");
		logInfo("账号 @".$data["name"]." 签到完成，成功 $count/$countAll 。");
    }
	
	private function getChaoHuaList($cookie){
		$ret = newHttp("https://weibo.com/ajax/profile/topicContent?tabid=231093_-_chaohua")
			->setCookie($cookie)
			->get()
			->asString();
		if(strpos($ret, "Sina Visitor System") > 0){
			logError("Cookie 已失效。");
			return null;
		}else{
			$ret = JSON_decode($ret);
		}
		
		if(!isset($ret->data->list)){
			logError("获取超话列表失败。");
			return null;
		}
		return $ret->data->list;
	}
}