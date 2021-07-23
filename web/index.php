<?php
require_once "../lib/scriptmanager.php";
require_once "../lib/accountmanager.php";
use ScriptManager as SM;
use AccountManager as AM;
SM\setPath("../script");
AM\setPath("../accounts.json");

$accounts = AM\query();
$scripts = SM\getAll();
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <script src="//cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link href="//cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.0.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="//cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.0.2/js/bootstrap.min.js"></script>
  <link href="index.css" rel="stylesheet">
  <script src="index.js"></script>
  <title>控制面板 - AutoCloudSign</title>
</head>

<body>
  <script>
    window.accounts = <?php echo json_encode($accounts, JSON_UNESCAPED_UNICODE)?>;
  </script>

  <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <span class="navbar-brand">控制面板</span>
  </nav>
  <br />

  <!--账号部分-->
  <div class="container">
    <h3 class="card-title">账号管理</h3>
    <button type=button class="btn btn-primary" id="account-btn-add" data-bs-toggle="modal" data-bs-target="#accountModalAdd">添加</button>
    <table class="table">
      <thead>
        <th style="width: 20%">网站</th>
        <th style="width: 20%">名称</th>
        <th style="width: 30%">备注</th>
        <th>操作</th>
      </thead>
      <tbody>
        <?php
        if($accounts != null){
          foreach ($accounts as $aid => $value) {
            $script = $value["script"]; //脚本 ID
            $scriptName = $scripts[$script]["name"]; //网站名称
            if(isset($scripts[$script]["icon"]))
              $icon =  $scripts[$script]["icon"]; //网站 logo
            else
              $icon = "no-icon.png";
            $name = $value["name"]; //账号名称
            $note = $value["note"]; //账号备注
            $cookie = $value["cookie"];

            $template = <<<DATA
          <tr class="account" data-aid="$aid" data-script="$script" data-name="$name" data-note="$note" data-cookie="$cookie">
          <td class="account-tb-script"><img src="$icon" style="margin-right: 7px;" class="website-icon">$scriptName</td>
          <td class="account-tb-name">$name</td>
          <td class="account-tb-note">$note</td>
          <td>
            <button type=button class="btn btn-light account-btn-edit" data-bs-toggle="modal" data-bs-target="#accountModalEdit">修改</button>
            <button type=button class="btn btn-danger account-btn-delete" data-bs-toggle="modal" data-bs-target="#accountModalDelete">删除</button>
          </td>
        </tr>

DATA;
          echo $template;
          }

        }
        else{
          echo '<tr><td colspan="4" style="text-align: center;" id="account-empty">什么都没有...</td</tr>';
        }

        ?>
      </tbody>
    </table>
  </div>

  <br/>
  <br/>

  <!--添加账号 模态框-->
  <div class="modal fade" id="accountModalAdd">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">添加账号</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="script">网站</label>
            <select name="script" id="account-modal-script" class="form-control">
              <?php
              $scripts = SM\getAll();
              foreach ($scripts as $id => $attr) {
                $name = $attr["name"];
                echo "<option value=\"$id\">$name</option>";
              }
              ?>
              
            </select>
          </div>
          <br/>

          <div class="form-group">
            <label for="name">名称</label>
            <input type="text" name="name" id="account-modal-name" class="form-control">
          </div>
          <br/>
          <div class="form-group">
            <label for="cookie">Cookie</label>
            <textarea id="account-modal-cookie" class="form-control" rows=10></textarea>
          </div>
          <br/>
          <div class="form-group">
            <label for="note">备注</label>
            <input type="text" name="note" id="account-modal-note" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="account-modalbtn-add">确定</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
        </div>
      </div>
    </div>
  </div>

<!--修改账号 模态框-->
  <div class="modal fade" id="accountModalEdit">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">修改账号</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="script">网站</label>
            <select name="script" id="account-modalEdit-script" class="form-control">
              <?php
              $scripts = SM\getAll();
              foreach ($scripts as $id => $attr) {
                $name = $attr["name"];
                echo "<option value=\"$id\">$name</option>";
              }
              ?>
              
            </select>
          </div>
          <br/>

          <div class="form-group">
            <label for="name">名称</label>
            <input type="text" name="name" id="account-modalEdit-name" class="form-control">
          </div>
          <br/>
          <div class="form-group">
            <label for="cookie">Cookie</label>
            <textarea id="account-modalEdit-cookie" class="form-control" rows=10></textarea>
          </div>
          <br/>
          <div class="form-group">
            <label for="note">备注</label>
            <input type="text" name="note" id="account-modalEdit-note" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="account-modalEditBtn-comfirm" data-action-aid="">确定</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
        </div>
      </div>
    </div>
  </div>

  <!--删除账号 模态框-->
  <div class="modal fade" id="accountModalDelete">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">删除账号</h4>
        </div>
        <div class="modal-body">
          <p>你确定要删除吗？</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" id="account-modalbtn-delete" data-action-aid="">删除</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
        </div>
      </div>
    </div>
  </div>



  <!--脚本部分-->
  <div class="container">
    <div class="row scripts">
      
      <div class="col-12">
        <h3>脚本管理</h3>
        <hr />

<?php
        $scripts = SM\getAll("../script");
      
        //循环输出所有脚本的信息
        foreach ($scripts as $id => $attr) {
          if(isset($attr["icon"]))
            $icon = $attr["icon"];
          else
            $icon = "no-icon.png";
          $name = $attr["name"];
          $enabled = $attr["enabled"] ? "已启用" : "已禁用";
          $btnStyle = $attr["enabled"] ? "btn-primary" : "btn-secondary";

          $template = <<<DATA

    <div class="row script" data-id="$id">
      <div class="col-md-2 script-name">
        <img src="$icon" class="website-icon">
        <span class="script-name">$name</span>
      </div>
      <div class="col-md-5"></div>
      <div class="col-md-5 script-buttons">
        <button type="button" class="btn $btnStyle script-btn-control" style="display: none">$enabled</button>
        <button type="button" class="btn btn-secondary script-btn-settings" style="display: none">设置</button>
        <button type="button" class="btn btn-danger script-btn-delete" data-bs-toggle="modal" data-bs-target="#deleteMsgbox">删除</button>
        </div>
    </div>

DATA;
          echo $template;
        }
?>

        <!--示例-->
        <div class="row script" data-id="bilibili" style="display: none;">
          <div class="col-2">
            <img src="https://www.bilibili.com/favicon.ico?v=1" class="script-icon">
            <span class="script-name">哔哩哔哩</span>
          </div>

          <div class="col-5"></div>
          <div class="col-5" class="script-buttons">
            <button type="button" class="btn btn-primary script-btn-enable">已启用</button>
            <button type="button" class="btn btn-secondary script-btn-settings">设置</button>
            <button type="button" class="btn btn-danger script-btn-delete" data-bs-toggle="modal" data-bs-target="#deleteMsgbox">删除</button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!--删除脚本 模态框-->
  <div class="modal fade" id="deleteMsgbox">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">删除</h4>
        </div>
        <div class="modal-body">
          <p>你确定要删除吗？</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" id="mbtn-delete">删除</button>
          <button type="button" class="btn btn-secondary" id="mbtn-cancel" data-bs-dismiss="modal">取消</button>
        </div>
      </div>
    </div>
  </div>

  <footer>
    
  </footer>

</body>
</html>