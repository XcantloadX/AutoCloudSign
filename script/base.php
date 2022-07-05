<?php

use WpOrg\Requests\Requests;
use WpOrg\Requests\Session;

abstract class Runner{
	protected $notification;
    protected $session;

    public function __construct(){
        $this->session = new Session();
    }

	/**
	 * 开始签到。此方法会被 start.php 调用
	 * @param  string $aid      账号 aid
	 * @param  array  $data     账号的信息。格式参见 accounts.json。
	 */
    public function run(string $aid, array $data){
        if($data["cookie"] == ""){
            logError("Cookie 为空。");
            return;
        }

        $this->session->headers["Cookie"] = $data["cookie"];
        $this->session->headers["User-Agent"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.157 Safari/537.36";
    }

    /**
     * 设置通知推送
     * @param NotificationBuilder $notification 实例
     */
    public function setNotification(NotificationBuilder $notification){
    	$this->notification = $notification;
    }
}