<?php

/*
To enable the initializer feature (https://help.aliyun.com/document_detail/89029.html)
please implement the initializer function as below：
function initializer($context) {
  $logger = $GLOBALS['fcLogger'];
  $logger->info('initializing');
}
*/

function handler($event, $context) {
    $GLOBALS["SERVERLESS"] = true;
    include "start.php";
    return "返回值随便写";
}


handler(null, null);