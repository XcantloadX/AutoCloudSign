<?php
abstract class Runner{
	protected $notification;

	/**
	 * 开始签到。此方法会被 start.php 调用
	 * @param  string $aid      账号 aid
	 * @param  array  $data 账号的信息。格式参见 accounts.json。
	 */
    abstract public function run(string $aid, array $data);

    /**
     * 设置通知推送
     * @param NotificationBuilder $notification 实例
     */
    public function setNotification(NotificationBuilder $notification){
    	$this->notification = $notification;
    }
}