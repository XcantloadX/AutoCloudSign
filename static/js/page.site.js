$(function(){
	//新建账号
	$("#newAccComplete").click(function(){
		if($("#newAccName").val() != "" && $("#newAccCookie").val() != ""){
			$("#newAccountModal").modal("hide");
		}
		else{
			alert("信息填写不完整。");
		}
	});

	$("#goBack").click(function(){
		window.location = "/";
	});
})
