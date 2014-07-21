<?php
require("./common.php");
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

include_once("./class/sql.inc.php");
$mysql=new sql_db($_SESSION['host'],$_SESSION['user'],$_SESSION['pwd'],$_SESSION['dbname']);
$mysql->sql_query("SET NAMES UTF8");
$sql = "show full fields from `".$_SESSION['TableName']."`";
$rs = $mysql->re_datas($sql);
$sql = "show index from `".$_SESSION['TableName']."`";
$showIndex = $mysql->re_datas($sql);
$primaryFieldArr = array();
foreach ($showIndex as $v) {
    if ($v['Key_name'] == 'PRIMARY') {
        $primaryFieldArr[] = $v['Column_name'];
    }
}
if (!empty($primaryFieldArr)) {
    $priTd = ' style="display:none;"';
}
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

foreach ($rs as $v)
{
    if (is_file(DAGGER_PATH_ROOT . "model/db/".$_SESSION['ModuleFileName']."ModelDB.php")) {
        $className = $_SESSION['ModuleFileName']."ModelDB";
        $o_db = new $className;
        $arr = $o_db->getFieldArr();
        $_SESSION[$_SESSION['TableName'] . "_" . $v['Field']] = empty($_SESSION[$_SESSION['TableName'] . "_" . $v['Field']]) ? $arr[$v['Field']]['name'] : $_SESSION[$_SESSION['TableName'] . "_" . $v['Field']];
    }
    $primary = in_array($v['Field'], $primaryFieldArr) ? ' checked' : '';
    $has_auto_increment |= $v['Extra'] == 'auto_increment' ? 1 : 0;
	$add_checked = $i!=0 ? "checked" : "";
    $show_ch_name = empty($_SESSION[$_SESSION['TableName'] . "_" . $v['Field']]) ? (empty($v['Comment']) ? strtoupper($v['Field']) : $v['Comment']) : $_SESSION[$_SESSION['TableName'] . "_" . $v['Field']];
	$str .= "<tr align=center><td>".$v['Field']."</td>
	<td><input type='text' name='name[".$v['Field']."]' value='".$show_ch_name."'></td>";
    if ($v['Extra'] == 'auto_increment') {
        $str .= str_replace("type='checkbox'", "type='checkbox' checked", "<td><input type='checkbox' name='empty[".$v['Field']."]' value='1'></td>");
    } else {
        $str .= "<td><input type='checkbox' name='empty[".$v['Field']."]' value='1'></td>";
    }
    $str .= "<td><select name='check[".$v['Field']."]'>".$type_option."</select></td>";
	$str .= "<td><input type='checkbox' id='show_field_{$v['Field']}' name='show_field[".$v['Field']."]' value='".$v['Field']."'></td>
	<td><input type='checkbox' id='add_field_{$v['Field']}' class='add_field' name='add_field[".$v['Field']."]' value='".$v['Field']."' ".$add_checked."></td>";
    $input_type_css = "";
    if ($add_checked != 'checked') {
        $input_type_css = "display:none";
    }
    $str .= "<td><div id='input_type_".$v['Field']."' style='".$input_type_css."'><select class='input_type' key='{$v['Field']}' name='input_type[".$v['Field']."]'><option value='input'>input</option><option value='textarea'>textarea</option><option value='richtext'>richtext</option><option value='select'>select</option><option value='multi_select'>multi-select</option><option value='radio'>radio</option><option value='checkbox'>checkbox</option><option value='time'>日期输入</option><option value='add_time'>添加时间</option><option value='modify_time'>修改时间</option><option value='img'>s3图片上传</option><option value='file'>s3文件上传</option></select></div></td>";
	$str .= "<td><input type='checkbox' name='search_field[{$v['Field']}]' value='{$v['Field']}'></td><td><input type='checkbox' name='order_field[{$v['Field']}]' value='{$v['Field']}'></td>
	<td{$priTd}><input type='checkbox' name='primary[{$v['Field']}]' value='{$v['Field']}'{$primary}></td>";
    $str .=  empty($_POST['logic_delete']) ? '' : "<td><input type='radio' name='logic_delete' value='".$v['Field']."'></td>";
    $str .= "</tr>
    <input type='hidden' name='type_field[".$v['Field']."]' value='" . $v['Type'] . "' id=''/>
    <input type='hidden' name='field[".$v['Field']."]' value='" . $v['Field'] . "' id=''/>";
    $str .= "<tr id='fk_tr_{$v['Field']}' style='display:none;'><td></td><td colspan='10'>
    外键数据源类型：<select class='fk_type' key='{$v['Field']}' name='fk_type[{$v['Field']}]'><option value=''>请选择</option><option value='db'>数据库</option><option value='file'>配置文件</option></select>
    <span id='fk_type_{$v['Field']}'></span>
    <span id='fk_type_key_value_{$v['Field']}'></span>
    </td></tr>";
	$i++;
}
$_SESSION['has_auto_increment'] = $has_auto_increment;
$title = empty($_POST['logic_delete']) ? '' : '<td>逻辑删除</td>';
?>
<html>
<head>
<title>模板构架</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://www.sinaimg.cn/jslib/js/jquery.1.8.2.js"></script>
<style>
body {text-align:center;padding-top:50px;background-color:#F7E8AC;}
table {font-size:12px; line-height:20px;border-color:#FF9900; border-style:groove}
th {background-color:#FF9900; font-size:14px; font-weight:bold;}
td {padding-left:5px; border-style:inherit; border:1px dashed #FF9900;}
</style>
<script>
function add_field(obj){
    if ($(this).attr('class') == 'add_field') {
        obj = $(this);
    }
    var t = obj.attr("checked");
    var v = obj.val();
    if (t == 'checked') {
        $("#input_type_"+v).show();
    } else {
        $("#input_type_"+v).hide();
    }
}
function input_type(obj){
    if ($(this).attr('class') == 'input_type') {
        obj = $(this);
    }
    var k = obj.attr('key');
    var v = obj.val();
    if (v == 'checkbox') {
        $("#show_field_"+k).attr('disabled', true);
        $("#show_field_"+k).css('display', 'none');
    } else if (v == 'add_time' || v == 'modify_time') {
        $("#add_field_"+k).attr('disabled', true);
        $("#add_field_"+k).css('display', 'none');
    } else {
        $("#add_field_"+k).removeAttr('disabled');
        $("#add_field_"+k).css('display', '');
        $("#show_field_"+k).removeAttr('disabled');
        $("#show_field_"+k).css('display', '');
    }
    if (v == 'select' || v == 'radio' || v == 'checkbox' || v=='multi_select') {
        $("#fk_tr_"+k).slideDown();
    } else {
        $("#fk_tr_"+k).slideUp();
    }
}
function fk_type(obj){
    if ($(this).attr('class') == 'fk_type') {
        obj = $(this);
    }
    var k = obj.attr('key');
    var v = obj.val();
    if (v == 'db') {
        $("#fk_type_"+k).html('载入中...');
        $("#fk_type_key_value_"+k).html('');
        $("#fk_type_"+k).load("./AjaxSelect.php?action=select&type=db&k="+k);
    } else if (v == 'file') {
        $("#fk_type_"+k).html('载入中...');
        $("#fk_type_key_value_"+k).html('');
        $("#fk_type_"+k).load("./AjaxSelect.php?action=select&type=file&k="+k);
    } else {
        $("#fk_type_"+k).html('');
        $("#fk_type_key_value_"+k).html('');
    }
    //$('[name="fk_type[type]"]').val('file');fk_type($('[name="fk_type[type]"]'));$('[name="fk_type_file[type]"]').val('DB');
}
function fk_type_db_table(obj){
    if ($(this).attr('class') == 'fk_type_db_table') {
        obj = $(this);
    }
    var k = obj.attr('key');
    var v = obj.val();
    if (v) {
        $("#fk_type_key_value_"+k).html('载入中...');
        $("#fk_type_key_value_"+k).load("./AjaxSelect.php?action=table&table="+v+"&k="+k);
    } else {
        $("#fk_type_key_value_"+k).html('');
    }
}
function fk_type_file(obj){
    if ($(this).attr('class') == 'fk_type_file') {
        obj = $(this);
    }
    var k = obj.attr('key');
    var v = obj.val();
    if (v) {
        $("#fk_type_key_value_"+k).html('载入中...');
        $("#fk_type_key_value_"+k).load("./AjaxSelect.php?action=file&file="+v+"&k="+k);
    } else {
        $("#fk_type_key_value_"+k).html('');
    }
}
$(document).ready(function(){
    $.ajaxSetup({async:false});
    $(".add_field").click(add_field);
    $(".input_type").change(input_type);
    $(".fk_type").change(fk_type);
    $(".fk_type_db_table").live('change', fk_type_db_table);
    $(".fk_type_file").live('change', fk_type_file);
    $.getScript("./setting/<?php echo $_SESSION['ModuleFileName'];?>.js");
});
</script>
</head>
<body>
<script>
function html_save(form) {
    form.html.value='<html>'+document.head.innerHTML + document.body.innerHTML+'</html>';
}
</script>
<form action="./finally.php" method="POST" >
<table align=center border=1>
<tr><th colspan="10">设置字段</th></tr>
<tr align=center><td>字段</td><td>填写中文名</td><td>添加时传入允许为空</td><td>类型检测</td><td>是否显示在列表</td><td>是否生成添加表单</td><td>表单类型</td><td>是否生成搜索</td><td>是否可排序</td></td><td<?php echo $priTd;?>>主键</td><?php echo $title;?></tr>
<?php echo $str;?>
<tr><td colspan=8 align=center><input type="hidden" name="html"><input type="submit" value="下一步"></td></tr>
</table>
</form>
</body>
</html>
