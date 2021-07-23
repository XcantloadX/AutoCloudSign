window.AM = {
	delete: function(aid, success){
		$.ajax({
			url: "api/account.php?action=delete",
			type: "POST",
			data: {
				aid: aid
			},
			success: success,
			error: function (data){
				//TODO toast 错误提示
				console.log(data);
			}
		});
	},

	add: function (data, success){
		$.ajax({
			url: "api/account.php?action=add",
			type: "POST",
			data: data,
			success: success,
			error: function (data){
				//TODO toast 错误提示
				console.log(data);
			}
		});
	},

	modify: function(data, success){
		$.ajax({
			url: "api/account.php?action=modify",
			type: "POST",
			data: data,
			success: success,
			error: function (data){
				//TODO toast 错误提示
				console.log(data);
			}
		});
	}

};

$(function(){
	//设置按钮
	$(".script-btn-settings").click(function(){
		let id = $(this).parents(".script").attr("data-id");
		window.open("script.php?id=" + id);
	});

	//启用禁用按钮
	$(".script-btn-enable").click(function(){
		let id = $(this).parents(".script").attr("data-id");
		$.ajax({
			url: ""
		});
	});

	//删除按钮
	$("#mbtn-cancel").click(function(){
		let id = $(this).parents(".script").attr("data-id");
		$.ajax({
			url: ""
		});
	});

	//添加账号-确认
	$("#account-modalbtn-add").click(function (){
		let data = {
			name: $("#account-modal-name").val(),
			note: $("#account-modal-note").val(),
			cookie: $("#account-modal-cookie").val(),
			script: $("#account-modal-script").val()
		};

		AM.add(data, function (data){
			console.log(data);
			if(data.code == 0){
				//TODO 避免刷新，加入新添加的表账号到表格
				window.location.reload();
				$("#account-modal-name").val("");
				$("#account-modal-note").val("");
				$("#account-modal-cookie").val("");
			}
		});
		
	});

	//修改账号按钮
	$(".account-btn-edit").click(function (){
		$("#account-modalEditBtn-comfirm").attr("data-action-aid", $(this).parents(".account").attr("data-aid"));
		$("#account-modalEdit-name").val($(this).parents(".account").attr("data-name")),
		$("#account-modalEdit-note").val($(this).parents(".account").attr("data-note")),
		$("#account-modalEdit-cookie").val($(this).parents(".account").attr("data-cookie")),
		$("#account-modalEdit-script").val($(this).parents(".account").attr("data-script"))
	});

	//修改账号-确认按钮
	$("#account-modalEditBtn-comfirm").click(function (){
		let data = {
			aid: $("#account-modalEditBtn-comfirm").attr("data-action-aid"),
			name: $("#account-modalEdit-name").val(),
			note: $("#account-modalEdit-note").val(),
			cookie: $("#account-modalEdit-cookie").val(),
			script: $("#account-modalEdit-script").val()
		};

		AM.modify(data, function (){
			window.location.reload();
		});
	});

	//删除账号按钮
	$(".account-btn-delete").click(function (){
		let aid = $(this).parents(".account").attr("data-aid");
		$("#account-modalbtn-delete").attr("data-action-aid", aid);
	});

	//删除账号-确认按钮
	$("#account-modalbtn-delete").click(function (){
		AM.delete($(this).attr("data-action-aid"), function (data){
			console.log(data);
			if(data.code == 0){
				//TODO 避免刷新，加入新添加的表账号到表格
				window.location.reload();
			}
		});
	});



});