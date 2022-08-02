<?php
/**
 * 是否为 Serverless 环境
 * @return bool 结果
 */
function isServerless() : bool{
    return isset($GLOBALS["SERVERLESS"]) && $GLOBALS["SERVERLESS"] == true;
}

/**
 * 是否为命令行环境
 */
function isCmd() : bool{
    return PHP_SAPI == "cli";
}