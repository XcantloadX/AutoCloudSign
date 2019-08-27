$(function(){
	if(navigator.userAgent.indexOf("MSIE") > -1){
	try{
		Toast.pop("你正在使用 IE 浏览器，可能出现未知错误，建议使用 Chrome。", "warn", 5);
	}
	catch(e){
		alert("你正在使用 IE 浏览器，可能出现未知错误，建议使用 Chrome。");
	}
}
});
