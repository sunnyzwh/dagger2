<?php
require("./common.php");
if ($_POST) {
    $js_str = '';
    $js_str .= "$('input[name=\"host\"]').val('{$_POST['host']}');\n";
    $js_str .= "$('input[name=\"dbname\"]').val('{$_POST['dbname']}');\n";
    $js_str .= "$('input[name=\"user\"]').val('{$_POST['user']}');\n";
    $js_str .= "$('input[name=\"pwd\"]').val('{$_POST['pwd']}');\n";
    $js_str .= "$('select[name=\"app\"]').val('{$_POST['app']}');\n";
    $js_str .= "$('select[name=\"db_mark\"]').val('{$_POST['db_mark']}');\n";
    file_put_contents("./setting/step_1.js", $js_str);
}
?>
<html>
<head>
<title>模板构架</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<style>
body {text-align:center;padding-top:50px;background-color:#F7E8AC;}
table {font-size:12px; line-height:20px;border-color:#FF9900; border-style:groove}
th {background-color:#FF9900; font-size:14px; font-weight:bold;}
td {padding-left:5px; border-style:inherit}
</style>
</head>
<body>
<?php
include_once("./class/sql.inc.php");
$dir = rtrim(dirname(__FILE__),"/")."/";
$dir = substr($dir, 0, -15);
if (empty($dir) || !is_dir($dir) || !is_file(rtrim($dir,"/")."/config/SysInitConfig.php"))
{
	echo "指定系统目录不在或不是系统框架";
	exit;
}
$_SESSION['dir'] = $dir;
$_SESSION['host'] = $_POST['host'];
$_SESSION['user'] = $_POST['user'];
$_SESSION['pwd'] = $_POST['pwd'];
$_SESSION['dbname'] = $_POST['dbname'];
$_SESSION['db_mark'] = $_POST['db_mark'];
$_SESSION['app'] = $_POST['app'];
$mysql=new sql_db($_POST['host'],$_POST['user'],$_POST['pwd'],$_POST['dbname']);
$sql = "show tables";
$rs = $mysql->re_datas($sql);
$str = "";
$key = "Tables_in_".$_POST['dbname'];
if (empty($rs)) {
    echo "没有找到可用的表，请重新填写数据库信息，<a href='./step_1.php'>返回</a>";
    exit;
}
foreach ($rs as $v)
{
	$str .= "<option value='".$v[$key]."'>".$v[$key]."</option>";
}

//echo $sql = "update xdown_soft set make = ".$_GET[c]." where id=".$_GET[id];
//$mysql->sql_query($sql);
?>
<div style="margin:0 auto;width:500px;">
<table align="center">
    <tr><th colspan="2">设置模块名</th></tr>
<form action="./DataReplace.php" method="post">
<tr><td>模块中文名称：</td><td><input type="text" name="ModuleName" />如：管理员用户</td></tr>
<tr><td>模块名称：</td><td><input type="text" name="ModuleUrlName" />一般为表名，如：admin_user</td></tr>
<tr><td>后台每页显示数据量：</td><td><input type="text" name="PageNum" value="20" size=5/></td></tr>
<tr><td>操作数据表：</td><td><select name="TableName">
<?php echo $str;?>
</select></td></tr>
<tr><td>使用逻辑删除：</td><td><input type="checkbox" name="logic_delete"/> 请确保有逻辑删除标识字段(下一步选择具体字段)</td></tr>
<tr><td colspan="2" align=center><input type="submit" name="submit" value="下一步" /></td></tr>
</form>
</div>
</body>
</html>
