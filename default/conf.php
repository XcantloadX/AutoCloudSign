<?php
//此脚本储存 conf.php 里的默认设置

class DefaultSettings{
    /**
     * 是否启用 UI 界面
     * 开启后会禁用 cookies.php
     */
    public static $enableWebUI = false;

    /**
     * 设置为 null 表示禁用
     * Qmsg QQ 消息推送（qmsg.zendee.cn）
     */
    public static $qmsg_key = null;
    /**
     * server酱 Turbo 版微信消息推送
     */
    public static $sct_key = null;

}

/**
 * 签到脚本所需要以及储存的数据会保存在这里。
 * 如无特别说明，填写方式如下
 * 
 * 以百度贴吧为例：
 * public static $TieBa = [["cookie" => "aaa"], ["cookie" => "bbb"], ..., ["cookie" => "nnn"]];
 * 
 * 若脚本支持自定义配置，例如若有 A、B 两个配置项，填写方式如下
 * public static $XXXX = [["cookie" => "", "A" => "xxx", "B" => "xxxx"]];
 */
class DefaultScriptStorage{

}