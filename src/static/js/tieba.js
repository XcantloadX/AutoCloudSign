var cookieEdit = null;
var logText = null;
var msg = null;

onload(function()
{
	cookieEdit = document.getElementById("cookie-edit");
	msg = document.getElementById("msg-cookie");
	logText = document.getElementById("log-text");
	
	GetCookie();
	LoadLog();
});


//获取 Cookie
function GetCookie()
{
	SendHTTP("api/cookie.php?method=get", "GET", "", function(http)
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
		msg.className = "msg msg-err";
		msg.style.display = "";
		RefreshMsg(msg);
		
		window.setTimeout(function(){ msg.style.display = "none"; }, 3000);
		return;
	}
			
	SendHTTP("api/cookie.php?method=set", "POST", cookieEdit.value, function(http)
	{
		var json = JSON.parse(http.responseText);
		
		//显示提示信息
		msg.innerText = json.msg;
		msg.className = json.err == 0 ? "msg msg-success" : "msg msg-err";
		msg.style.display = "";
		RefreshMsg(msg);
		
		window.setTimeout(function(){ msg.style.display = "none"; }, 3000);
		
	});
}

//加载 log 内容
function LoadLog(){
	var pre = $("#log")[0];
	
	$.ajax({
		url: "api/getLog.php", 
		async: true,
		success: function(data){
			var json = JSON.parse(data);
			pre.innerText = json.log;
			
			//自动滚动到 <pre> 底部
			pre.scrollTop = pre.scrollHeight;
		}
	});

}