<?php
/**
 * 是否为 Serverless 环境
 * @return bool 结果
 */
function isServerless(){
    return isset($GLOBALS["SERVERLESS"]) && $GLOBALS["SERVERLESS"] == true;
}