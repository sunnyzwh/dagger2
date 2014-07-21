<?PHP
session_start();
require_once("../../config/SysInitConfig.php");
require_once("../../libs/DaggerSysInitConfig.php");
//*********************************************************************************
$dir = $_SESSION['dir'];
if (empty($_SESSION['ModuleName']) || empty($dir)){
    die('SESSION已过期，请重新创建<a href="./index.php">返回</a>');
}
//DB类*****************************************************************************
if (is_file($dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php")) {
    $out .= $dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php DB类已存在，只能对字段信息进行更新...<br>";
    $rs = file_get_contents($dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php");
} else {
    $rs = file_get_contents("../admin/module/TemplateModelDB.php");
    $rs = str_replace('{%ModuleName%}',$_SESSION['ModuleName'], $rs);
    $rs = str_replace('{%ModuleVarName%}',$_SESSION['ModuleVarName'], $rs);
    $rs = str_replace('{%ModuleFileName%}',$_SESSION['ModuleFileName'], $rs);
    $rs = str_replace('{%TableName%}',$_SESSION['TableName'], $rs);
}
$arr = explode("//field_arr start", $rs);
list($field_str, $ext) = explode("//field_arr end", $arr[1]);
$field_str = str_replace(array(" ", "\t", "\n", "\r"), "", $field_str);

$check_md5 = substr($ext, 1, 32);
if (md5($field_str) != $check_md5 && !empty($field_str)){
    $out .=  $dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php DB类字段配置已手动修改，自动生成配置写入不成功！<br>";
} else {
    $out .=  $dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php DB类字段配置信息已按设置更新！<br>";
    $field_arr = array();
    $primary_arr = array();
    foreach ($_POST['field'] as $k => $v) {
        preg_match("/([^\(]*)\((.*)\)/", $_POST['type_field'][$k], $match);
        $_POST['empty'][$k] = $_POST['empty'][$k] ? 0 : 1;
        $validate = $_POST['check'][$k] ? $_POST['empty'][$k] . '_' . $_POST['check'][$k] : $_POST['empty'][$k];
        $_SESSION[$_SESSION['TableName'] . "_" . $v] = $_POST['name'][$k];
        $field_arr[$v] = array(
            'name' => $_POST['name'][$k],
            'type' => $match[1],
            'max_length' => $match[2],
            'validate' => $validate
        );
        if ($_POST['primary'][$k] == true) {
            $primary_arr[] = $v; 
        }
    }

    $str = var_export($field_arr, true);
    $str = "\nprotected \$field_arr = " . $str . ";\n    ";
    $str = str_replace("\n", "\n    ", $str);

    $rs = substr_replace($rs, $str, strpos($rs, "//field_arr start") + strlen("//field_arr start"), strpos($rs, "//field_arr end") - strpos($rs, "//field_arr start") - strlen("//field_arr start"));
    $md5 = md5(str_replace(array(" ", "\t", "\n", "\r"), "", $str));
    $rs = substr_replace($rs, " " . $md5, strpos($rs, "//field_arr end") + strlen("//field_arr end"), 32);
    $out .= $dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php DB类写入成功，内容如下：<br>";
    $out .= "<textarea name='null' rows='5' cols='100'>{$rs}</textarea><br>";

    $s = file_put_contents($dir . "/model/db/{$_SESSION['ModuleFileName']}ModelDB.php", $rs);
    if(!$s){
            echo "没有足够的权限生成文件, 请检查 {$dir}/model/db/ 权限\n";
    }

}

$out .= "<div style=\"font-size:20px;padding-top:20px;\">您可以通过<span style=\"color:red;\">new {$_SESSION['ModuleFileName']}ModelDB();</span>来初始化这个DB类</div>";
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
textarea {font-size:12px;}
</style>
</head>
<body>
<table align=center border=1>
<tr><th>生成结果</th></tr>
<tr><td><?php echo $out;?></td></tr>
</table>
</body>
</html>
