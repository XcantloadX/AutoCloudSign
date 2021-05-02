<?php
include_once "lib/http.php";
include_once "conf.php";

/**
 * 推送 QQ 消息
 * @param string $msg 消息
 * @return object $ret Qmsg API 返回结果
 */
function pushQQMsg(string $msg) : object{
	global $qmsg_key;
	$ret = newHttp("https://qmsg.zendee.cn/send/".$qmsg_key)
		->buildQuery(array("msg" => $msg))
		->get()
		->asJSON();
	return $ret;
}

function pushWechatMsg(string $title, string $msg) : object{
	global $sct_key;
	$ret = newHttp("https://sctapi.ftqq.com/".$sct_key.".send")
		->buildQuery(array("title" => $title, "desp" => $msg))
		->get()
		->asJSON();
	return $ret;
}

class NotificationBuilder{
	private $msg = ""; //纯文本版本
	private $mdMsg = ""; //markdown 版本
	private $title = "";

	public function setTitle(string $title){
		$this->title = $title;
	}

	/**追加一行消息
	 * @param string $rawMsg 原始消息
	 * @param string $plainMsg 普通消息，使用 %s 来引用原始消息。留空表示与原始消息相同。
	 * @param string $mdMsg Markdown 消息，使用 %s 来引用原始消息。留空表示与原始消息相同。
	 */
	public function append(string $rawMsg, string $plainMsg = null, string $mdMsg = null){
		if(!isset($mdMsg) || $mdMsg == null || $mdMsg == "")
			$mdMsg = "%s";
		if(!isset($plainMsg) || $plainMsg == null || $plainMsg == "")
			$plainMsg = "%s";

		$this->msg = $this->msg.sprintf($plainMsg, $rawMsg).PHP_EOL;
		$this->mdMsg = $this->mdMsg.sprintf($mdMsg, $rawMsg)."  ".PHP_EOL; //markdown 规定两个空格强制换行
	}


	public function asString() : string{
		return $this->msg;
	}

	public function push(){
		global $qmsg_key, $sct_key;
		if($qmsg_key != null){
			$ret = pushQQMsg($this->msg);
			if(!$ret->success){
				//TODO 错误处理
			}
		}
		if($sct_key != null){
			$ret = pushWechatMsg($this->title, $this->mdMsg);
			if($ret->code != 0){
				$errmsg = $ret->data->error;
			}
		}
	}
}