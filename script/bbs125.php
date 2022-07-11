<?php
//@id           BBS125
//@name         精易论坛
//@site         bbs.125.la
//@note         必须用国内 ip 访问

//TODO 尝试解决 必须用国内 ip 访问 （？）
class BBS125 extends Runner{
    public function run(string $aid, array &$data){
        parent::run($aid, $data);
        $formhash = $this->getFormHash();
        $response = $this->session->post(
            "https://bbs.125.la/plugin.php?id=dsu_paulsign:sign&operation=qiandao&infloat=1",
            array(),
            "formhash=$formhash&submit=1&targerurl=%2Fthread-14695011-1-1.html&todaysay=%E3%80%90%E9%A6%96%E5%8F%91%E3%80%91%E4%B8%80%E4%B8%AAAPI%E6%9C%89%E4%BA%BF%E4%B8%AA%E5%8A%9F%E8%83%BD%EF%BC%88NtQuerySystem+...&qdxq=kx"
        )->body;
        $ret = json_decode($response);
        //错误检查
        if(!isset($ret->status)){
            logError("错误：".$ret);
            return;
        }
        $status = $ret->status;
        if($status == 0){
            logInfo("今天已经签到过了。");
        	$this->notification->append("今天已经签到过了。");
        	return;
        }
        else if($status != 1){
            logInfo("未知错误 status={$status}。");
            logInfo("已终止");
        	$this->notification->append("未知错误 status={$status}。");
        	$this->notification->append("已终止");
        	return;
        }

        $credit = $ret->data->credit; //获得精币
        $days = $ret->data->days; //总共签到天数
        $mdays = $ret->data->mdays; //本月签到天数
        logInfo("获得 $credit 精币，本月已签 $mdays/".date("t")." 天");
        $this->notification->append("获得 $credit 精币，本月已签 $mdays/".date("t")." 天");
    }

    private function getFormHash() : string{
        $ret = $this->session->get("https://bbs.125.la/plugin.php?id=dsu_paulsign:sign")->body;
    	preg_match("/<input type=\"hidden\" name=\"formhash\" value=\"(.*?)\"/", $ret, $matches);
    	if(count($matches) <= 0){
            logError("获取 formhash 失败！");
    		$this->notification->append("获取 formhash 失败！");
    		return "";
    	}
    	return $matches[1];
    }
}
