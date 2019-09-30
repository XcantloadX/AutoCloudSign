window.Highlight = window.Highlight || {};

const COLOR_INFO = "#48BB31";
const COLOR_WARNING = "#BBBB23";
const COLOR_ERROR = "#FF0006";
const COLOR_DEBUG = "#00700B";
const _template = "<span style=\"color: {color}\">{text}</span>";

//替换文本
var _replace = function(str, arr){
	for(var k in arr){
		str = str.replace("{" + k + "}", arr[k]); //替换字符串
		console.log(k + "|" + arr[k] + "|" + str);
	}
	
	return str;
}

//高亮文本，返回高亮后的 Html 代码
//@param str 要高亮的日志文本
window.Highlight.process = function(str){
	var lines = str.split("\n");
	var html = "";
	
	for(var i = 0; i < lines.length; i++){
		if(lines[i].search("Warning") >= 0)
			html += _replace(_template, {"color": COLOR_WARNING, "text": lines[i]});
		else if(lines[i].search("Error") >= 0)
			html += _replace(_template, {"color": COLOR_ERROR, "text": lines[i]});
		else
			html += _replace(_template, {"color": COLOR_INFO, "text": lines[i]});
	}
	
	return html;
}

