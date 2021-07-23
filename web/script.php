<?php
require "../lib/scriptmanager.php";

//参数检查
if(isset($_GET["id"])){
  $id = $_GET["id"];
}
else{
  header("HTTP/1.1 400");
  echo '<h1 style="text-align: center">请求错误</h1>';
  echo "<center>参数 id 缺失</center>";
  die();
}

if($id == ""){
  header("HTTP/1.1 400");
  echo '<h1 style="text-align: center">请求错误</h1>';
  echo "<center>参数 id 为空</center>";
  die();
}

//获取脚本信息
$attr = getAttributes($id, "../script");
?>

<html>
<head>
  <title><?php echo $attr["name"] ?> - AutoCloudSign</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <script src="//cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link href="//cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.0.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="//cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.0.2/js/bootstrap.min.js"></script>
</head>

<body>
  <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <span class="navbar-brand"><?php echo $attr["name"] ?> - 控制面板</span>
  </nav>
  <br />

  




  <footer>
    
  </footer>
  
</body>
</html>