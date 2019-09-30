window.Toast = window.Toast || {};

var ICON_ELEMENT = "<i class=\"{class}\"></i>";
var icons = {
	new: function(className){
		return ICON_ELEMENT.replace("{class}", className);
	},
};

icons = {
	info: icons.new("fa fa-info-circle"),
	success: icons.new("fa fa-check"),
	warn: icons.new("fa fa-exclamation-triangle"),
	error: icons.new("fa fa-times"),
};

//TODO: 支持同时弹出多个信息


//弹出新信息
//@param timeout 超时时间（秒）
Toast.pop = function(text, type, timeout){
	var div = document.createElement("div");
	
	div.innerText = text; //设置文本
	div.className = "msg msg-float " + "msg-" + type; //设置 class
	
	//设置图标
	div.innerHTML = icons[type] + div.innerHTML;
	
	//超时设置
	if(timeout != undefined && timeout > 0){
		window.setTimeout(Toast.clear, timeout * 1000, div);
	}
	
	//应用
	document.body.append(div);
	
	div.style.marginLeft = -div.clientWidth / 2; //居中
	return div;
}

//错误代码转换为类型
Toast.err2type = function(errCode){
	if(errCode != 0)
		return "error";
	return "success";
}

//清除信息
Toast.clear = function(msg){
	document.body.removeChild(msg);
}