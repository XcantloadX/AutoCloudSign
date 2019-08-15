var cookieEdit = null;
var logText = null;
var msg = null;

window.onload = function()
{
	cookieEdit = document.getElementById("cookie-edit");
	msg = document.getElementById("msg")
	logText = document.getElementById("log-text");
	
	GetCookie();
	GetLog();
}


//获取 Cookie
function GetCookie()
{
	SendHTTP("cookie.php?method=get", "GET", "", function(http)
	{
		var json = JSON.parse(http.responseText);
		cookieEdit.value = json.cookie;
	});
}

//设置 Cookie
function SetCookie()
{
	if(cookieEdit.value == null || cookieEdit.value == "")
	{
		msg.innerText = "Cookie 不能为空！";
		return;
	}
			
	SendHTTP("cookie.php?method=set", "POST", cookieEdit.value, function(http)
	{
		var json = JSON.parse(http.responseText);
		msg.innerText = json.msg;
	});
}

//获取 log 内容
function GetLog()
{
	SendHTTP("getLog.php", "GET", "", function(http)
	{
		var json = JSON.parse(http.responseText);
		logText.innerText = json.log;
	});
}

//发送 HTTP 请求
function SendHTTP(url, method, postData, onDataLoaded)
{
	var http = new XMLHttpRequest();
	http.open(method, url, true);
	http.onreadystatechange = function()
	{
		if(http.readyState == 4)
			onDataLoaded(http);
	}
	
	http.send(postData);
}