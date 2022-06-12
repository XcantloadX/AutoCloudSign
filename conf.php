<?php

class Settings{
    //---------一般------------
    /**
     * 是否启用 UI 界面
     * 开启后会禁用 cookies.php
     */
    public static $enableWebUI = false;

    //---------通知------------
    //设置为 null 表示禁用
    //Qmsg QQ 消息推送（qmsg.zendee.cn）
    public static $qmsg_key = null;
    //server酱 Turbo 版微信消息推送
    public static $sct_key = null;

    //---------Cookie------------
    //conf.php 中 $enableWebUI = true 的情况下这里的 Cookie 无效
    public static $cookie = array(
        /*
        填写示例：
        注意最后一个不需要加逗号
        "TieBa" => array(
            "账号1",
            "账号2",
            "账号3"
        );
        */

     	//百度贴吧
        "TieBa" => array(
			
        ),

        //哔哩哔哩
        "Bilibili" => array(
			
		),

        //网易云音乐
        "CloudMusic" => array(

		),


        //小黑盒
        "XiaoHeiHe" => array(

        ),

        //和彩云
        "HeCaiYun" => array(

        ),

        //精易论坛 bbs.125.la
        "BBS125" => array(

        ),
		
		//微博超话
		"WeiBoChaoHua" => array(
		
		),
    );
}