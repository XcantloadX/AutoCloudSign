const signer = [
	{
		name: "百度贴吧",
		logo: "static/image/bdtieba.png",
		state: "已签到",
		icon: "success",
		link: "tieba.html",
		class: "stat-signed"
	},
	
	{
		name: "百度知道",
		logo: "static/image/bdzhidao.png",
		state: "暂未实现自动签到",
		icon: "warn",
		link: "",
		class: "stat-warn"
	},
	
	{
		name: "Bilibili",
		logo: "static/image/bilibili.png",
		state: "正在开发",
		icon: "warn",
		link: "",
		class: "stat-warn"
	},
	
	{
		name: "网易云音乐",
		logo: "static/image/cloudmusic.png",
		state: "正在开发",
		icon: "warn",
		link: "",
		class: "stat-warn"
	},
	
	{
		name: "MCBBS",
		logo: "static/image/minecraft.png",
		state: "正在开发",
		icon: "warn",
		link: "",
		class: "stat-warn"
	}
];

const template = `
<div class="signer">
	<img class="logo" width="64" height="64" src="{logo}" alt="{name} LOGO" />
	<p class="name">{name}</p>
	
	<p>状态：<span class="{class}">{icon}{state}</span></p>
	<a class="btn btn-link" href="{link}">查看更多</a>
</div>
`;

//输出所有支持的网站
function writeSigners(){
	
	//循环所有网站
	for(var i = 0; i < signer.length; i++){
		var code = template;
		
		if(!signer[i].link || signer[i].link == "")
			signer[i].link = "javascript: void(0);";
		
		//替换参数
		code = code.replace(/{name}/g, signer[i].name);
		code = code.replace(/{logo}/g, signer[i].logo);
		code = code.replace(/{link}/g, signer[i].link);
		code = code.replace(/{state}/g, signer[i].state);
		code = code.replace(/{icon}/g, (signer[i].icon && signer[i].icon != "") ? window.Icon[signer[i].icon] : "");
		code = code.replace(/{class}/g, signer[i].class || "stat-signed");
		
		document.write(code); //输出
	}
}