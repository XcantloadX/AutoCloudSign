<?php
if(!is_array($_GET) || !isset($_GET["site"]) || !isset($_GET["name"])){
	echo "参数不正确。";
	exit;
}
?>
<!doctype html>
<html>
	<head>
		<title><?php echo $_GET["name"]; ?> - 云签到</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
		<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="../static/js/page.site.js"></script>
		<link href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
		<style>
		.accList{
			margin-top: 20px;

		}

		.accList li{
			height: 4em;
		}

		.accList button{
			margin-left: 0.5em;
		}
		</style>
	</head>
	
	<body>
		<nav class="navbar navbar-default">
			<div class="navbar navbar-nav">
				<button class="btn btn-default navbar-btn" id="goBack">返回</button>
			</div>
			<div class="navbar-header"><span class="navbar-brand"><?php echo $_GET["name"]; ?></span></div>
		</nav>
		
		<div class="container">
			
			<div class="panel panel-default">
				<div class="panel-heading">账号</div>
				<div class="panel-body">
					<button class="btn btn-primary" id="btn-add" data-toggle="modal" data-target="#newAccountModal">添加</button>

					<table class="table">
						<thead>
							<tr>
								<th>名称</th>
								<th>操作</th>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td>TEST</td>
								<td>
									<button class="btn btn-danger" id="delAcc">删除</button>
									<button class="btn btn-default" id="editAcc">修改</button>
								</td>
							</tr>


						</tbody>
					</table>

				</div>
			</div>
			
			<div class="modal fade" id="newAccountModal" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title">添加新账号</h4>
						</div>

						<div class="modal-body">
							<form>
								<div class="form-group">
									<label for="newAccName">名称</label>
									<input class="form-control" type="text" id="newAccName"/>
								</div>
								<div class="form-group">
									<label for="newAccCookie">Cookie</label>
									<textarea class="form-control" id="newAccCookie" rows="5"></textarea>
								</div>
							</form>
						</div>

						<div class="modal-footer">
							<button class="btn btn-primary" id="newAccComplete">提交</button>
							<button class="btn btn-default" data-dismiss="modal">关闭</button>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default" style="display: none">
				<div class="panel-heading">日志</div>
				<div class="panel-body">
					<div id="log"><?php ?></div>
					<br>
					<button class="btn btn-default" id="">立即执行</button>
				</div>
			</div>
			
			
		</div>
	</body>
</html>