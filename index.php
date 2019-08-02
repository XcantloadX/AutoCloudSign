<html>
	
	<head>
		<meta charset="utf-8" />
		<title>云签到设置</title>
	</head>
	
	<body>
		<!--设置 Cookie-->
		<div>
			
			<form method="post" action="">
				<p>当前 Cookie：</p>
				<textarea id="cookie" rows=20 cols=50><?php
				//如果 COOKIE 不存在则自动创建
				if(!file_exists("COOKIES"))
					fclose(fopen("COOKIES", "w"));
		
				echo file_get_contents("COOKIES");
				?></textarea>
				<br />
				<input type="button" value="保存" onclick="send();"/>
			</form>
			<p id="msg"></p>
		</div>
		
		<script language="javascript">
		function send()
		{
			var cookie = document.getElementById("cookie").value;
			var msg = document.getElementById("msg");
			
			if(cookie == null || cookie == "")
			{
				msg.innerText = "Cookie 不能为空！";
				return;
			}
				
			
			var http = new XMLHttpRequest();
			http.open("POST", "setCookie.php", true); //异步请求
			http.onreadystatechange = function()
			{
				if(http.readyState == 4)
					msg.innerText = http.responseText;
			}
			
			http.send("cookie=" + cookie);
		}
		</script>
		
		<hr style="margin-top: 30px; margin-bottom: 30px;"/>
		
		<!--查看 log-->
		<p>当前 Log：</p>
		<pre id="log" style="border: 1px solid"><?php
		if(file_exists("log.txt"))
			echo file_get_contents("log.txt");
		?></pre>
	</body>
	
</html>