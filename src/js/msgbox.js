var ICON_WARN = "<i class=\"fa fa-exclamation-triangle\"></i>";
var ICON_ERR = "<i class=\"fa fa-ban\"></i>";
var ICON_INFO = "<i class=\"fa fa-info-circle\"></i>"

onload(function()
{
	
	InitAllBoxes();
});

//初始化所有信息框
function InitAllBoxes()
{
	var boxes = document.getElementsByClassName("msg");
	for(var i = 0; i < boxes.length; i++)
	{
		InitBox(boxes[i]);
	}
}

//初始化单个休信息框
function InitBox(msg)
{
	var icon = ""; //图标
	
	if(msg.classList.contains("msg-warn"))
		icon = ICON_WARN;
	else if(msg.classList.contains("msg-err"))
		icon = ICON_ERR;
	else
		icon = ICON_INFO;
	
	msg.innerHTML = icon + msg.innerHTML;
}

//刷新信息框
function RefreshMsg(msg)
{
	InitBox(msg);
}