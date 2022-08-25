<?php
use WpOrg\Requests\Session;

abstract class Runner{
	protected $notification;
    protected $session;

    public function __construct(){
        $this->session = new Session();
    }

	/**
	 * 开始签到。此方法会被 start.php 调用
	 * @param  string $aid      账号 aid，保留参数。
	 * @param  array  $data     储存的数据，包括 Cookie 以及脚本自定义数据。参数使用引用传递，对其进行的修改将会被保存。
	 */
    public function run(string $aid, array &$data){
        $this->session->headers["Cookie"] = $data["cookie"];
        $this->session->headers["User-Agent"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.157 Safari/537.36";

        if(!isset($data["name"]))
            $data["name"] = $this->getName();
    }

    /**
     * 设置通知推送
     * @param NotificationBuilder $notification 实例
     */
    public function setNotification(NotificationBuilder $notification){
    	$this->notification = $notification;
    }

    /**
     * 获取脚本的默认设置（储存在 ScriptStorage 里的）
     * @return array 默认设置
     */
    public function getDefaultSettings() : array{
        return array("cookie" => "");
    }

    /**
     * 将用户设置里缺少的项用默认设置补上
     * @param array $user 用户设置
     * @return array 完整的设置
     */
    public function mergeSettings(array $user) : array{
        if(!isset($user["cookie"]))
            return $this->getDefaultSettings();
        else
            return $user;
    }

    /**
     * 获取当前账号的用户名
     * @return string 用户名
     */
    public function getName() : string{
        return "";
    }
}