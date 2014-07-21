<?php
session_start();
$dir = rtrim($_SESSION['dir'],"/")."/";
if (empty($_SESSION['dir']) || !is_dir($_SESSION['dir']))
{
	echo "指定系统目录不在";
	exit;
}
$_SESSION['ModuleName'] = $_POST['ModuleName'];
$_SESSION['ModuleUrlName'] = $_POST['ModuleUrlName'];
$name = explode('_', $_POST['ModuleUrlName']);
for($i = 0, $len = count($name); $i < $len; $i++ ) {
    if ($i == 0) {
        $name[$i] = strtolower($name[$i]);
    } else {
    	$name[$i] = ucfirst(strtolower($name[$i]));
    }
}

$_SESSION['ModuleVarName'] = implode('', $name);
$_SESSION['ModuleFileName'] = ucfirst($_SESSION['ModuleVarName']);
$_SESSION['TableName'] = $_POST['TableName'];
$_SESSION['PageNum'] = $_POST['PageNum'];

include_once("../admin/class/sql.inc.php");
$mysql=new sql_db($_SESSION['host'],$_SESSION['user'],$_SESSION['pwd'],$_SESSION['dbname']);
$sql = "show full fields from `".$_SESSION['TableName']."`";
$rs = $mysql->re_datas($sql);
$str = "";
$i = 0;
$type_option = '<option value="">不检查</option>
<option value="mobile">手机</option>
<option value="number">数字</option>
<option value="email">邮箱</option>
<option value="year">年份</option>
<option value="month">月份</option>
<option value="day">日期</option>
<option value="image">有效图片地址</option>
<option value="url">URL地址</option>
<option value="postcode">邮编</option>
<option value="resource">有效HTTP资源</option>
<option value="datatime">日期时间</option>
<option value="idcard">身份证</option>
<option value="ipv4">ipv4</option>
<option value="ipv6">ipv6</option>';
$has_auto_increment = false;

if (is_file("../../model/db/{$_SESSION['ModuleFileName']}ModelDB.php")) {
    require_once("../../libs/model/BaseModelDBConnect.php");
    require_once("../../libs/model/BaseModelDB.php");
    require_once("../../model/db/{$_SESSION['ModuleFileName']}ModelDB.php");
}
foreach ($rs as $v)
{
    if (class_exists($_SESSION['ModuleFileName']."ModelDB")) {
        $className = $_SESSION['ModuleFileName']."ModelDB";
        $o_db = new $className;
        $arr = $o_db->getFieldArr();
        $_SESSION[$_SESSION['TableName'] . "_" . $v['Field']] = empty($_SESSION[$_SESSION['TableName'] . "_" . $v['Field']]) ? $arr[$v['Field']]['name'] : $_SESSION[$_SESSION['TableName'] . "_" . $v['Field']];
    }
    $primary = $v['Extra'] == 'auto_increment' ? ' checked' : '';
    $has_auto_increment |= $v['Extra'] == 'auto_increment' ? 1 : 0;
	$add_checked = $i!=0 ? "checked" : "";
    $show_ch_name = empty($_SESSION[$_SESSION['TableName'] . "_" . $v['Field']]) ? strtoupper($v['Field']) : $_SESSION[$_SESSION['TableName'] . "_" . $v['Field']];
	$str .= "<tr align=center><td>".$v['Field']."</td>
	<td><input type='text' name='name[".$i."]' value='".$show_ch_name."'></td>";
    if ($v['Extra'] == 'auto_increment') {
        $str .= str_replace("type='checkbox'", "type='checkbox' checked", "<td><input type='checkbox' name='empty[".$i."]' value='1'></td>");
    } else {
        $str .= "<td><input type='checkbox' name='empty[".$i."]' value='1'></td>";
    }
    $str .= "<td><select name='check[".$i."]'>".$type_option."</select></td>";
    $str .= "</tr>
    <input type='hidden' name='type_field[".$i."]' value='" . $v['Type'] . "' id=''/>
    <input type='hidden' name='field[".$i."]' value='" . $v['Field'] . "' id=''/>";
	$i++;
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
td {padding-left:5px; border-style:inherit; border:1px dashed #FF9900;}
</style>
</head>
<body>
<form action="./finally.php" method="POST">
<table align=center border=1>
<tr><th colspan="10">设置字段</th></tr>
<tr align=center><td>字段</td><td>填写中文名</td><td>添加时传入允许为空</td><td>类型检测</td><?php echo $title;?></tr>
<?php echo $str;?>
<tr><td colspan=8 align=center><input type="submit" value="下一步"></td></tr>
</table>
</form>
</body>
</html>