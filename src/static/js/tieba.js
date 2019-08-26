var cookieEdit = null;
var logText = null;
var msg = null;

$(function(){
	cookieEdit = document.getElementById("cookie-edit");
	msg = document.getElementById("msg-cookie");
	logText = document.getElementById("log-text");
	
	GetCookie();
	LoadLog();
});


//获取 Cookie
function GetCookie(){
	$.ajax({
		url: "api/cookie.php?method=get",
		success: function(data){
			var json = JSON.parse(data);
			if(json.err != 0)
				Toast.pop(json.msg, "error", 3);
			cookieEdit.value = json.cookie;
		}
	});
}

//设置 Cookie
function SetCookie(){
	if(cookieEdit.value == null || cookieEdit.value == ""){
		Toast.pop("Cookie 不能为空！", "error", 3);
		return;
	}
	
	$.ajax({
		url: "api/cookie.php?method=set",
		type: "POST",
		data: cookieEdit.value,
		success: function(data){
			var json = JSON.parse(data);
			Toast.pop(json.msg, json.err == 0 ? "success" : "error", 3);
		}
	});
}

//加载 log 内容
function LoadLog(){
	var pre = $("#log")[0];
	
	$.ajax({
		url: "api/getLog.php", 
		success: function(data){
			var json = JSON.parse(data);
			pre.innerText = json.log;
			
			//自动滚动到 <pre> 底部
			pre.scrollTop = pre.scrollHeight;
		}
	});
}