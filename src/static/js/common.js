//网页加载完成事件
function onload(func)
{
	window.addEventListener("load", func, false);
}

//发送 HTTP 请求
function SendHTTP(url, method, postData, onDataLoaded)
{
	var http = new XMLHttpRequest();
	http.open(method, url, true);
	http.onreadystatechange = function()
	{
		if(http.readyState == 4 && onDataLoaded != null)
			onDataLoaded(http);
	}
	
	http.send(postData);
}