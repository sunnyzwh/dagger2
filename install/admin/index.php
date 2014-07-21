<html>
<head>
<title>后台应用构架搭建</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<style>
body {text-align:center;padding-top:50px;background-color:#F7E8AC;}
table {font-size:12px; line-height:20px;border-color:#FF9900; border-style:groove}
th {background-color:#FF9900; font-size:14px; font-weight:bold;}
td {padding-left:5px; border-style:inherit}
</style>
</head>
<body>
<div style="margin:0 auto;width:800px;text-align:center;">
<?php
function file_list($src_path, $dst_path){
    if ($handle = opendir($src_path)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && substr($file, 0, 1) != '.') {
                if (is_dir($src_path."/".$file)) {
                    $dir = $src_path."/".$file;
                    $GLOBALS['out'] .= '创建目录：' . $dst_path."/".$file;
                    $GLOBALS['out'] .= "<br>\n";
                    mkdir($dst_path."/".$file);
                    file_list($src_path."/".$file, $dst_path."/".$file);
                } else {
                    $file_path = $src_path."/".$file;
                    $rs = file_get_contents($file_path);
                    $GLOBALS['out'] .= '写入文件：' . $dst_path."/".$file;
                    $GLOBALS['out'] .= "<br>\n";
                    file_put_contents($dst_path . "/{$file}", $rs);
                }
            }
        }
    }
}
$dir = rtrim(dirname(__FILE__),"/")."/../../app/admin";
$out = '';
if (!is_dir($dir)) :
    $str = '';
    $codeDir = rtrim(dirname(__FILE__),"/")."/../../app";
    if (@file_put_contents($codeDir . '/.test.test', '') === false) {
        $str .= './app目录不可写，请调整目录权限（0777)，然后再刷新页面<br>';
    } else {
        @unlink($codeDir . '/.test.test');
    }
    $codeDir = rtrim(dirname(__FILE__),"/")."/../../model";
    if (@file_put_contents($codeDir . '/.test.test', '') === false) {
        $str .= './model目录不可写，请调整目录权限（0777)，然后再刷新页面<br>';
    } else {
        @unlink($codeDir . '/.test.test');
    }
    $codeDir = rtrim(dirname(__FILE__),"/")."/../../model/db";
    if (@file_put_contents($codeDir . '/.test.test', '') === false) {
        $str .= './model/db目录不可写，请调整目录权限（0777)，然后再刷新页面<br>';
    } else {
        @unlink($codeDir . '/.test.test');
    }
    $codeDir = rtrim(dirname(__FILE__),"/")."/setting";
    if (@file_put_contents($codeDir . '/.test.test', '') === false) {
        $str .= './install/admin/setting目录不可写，请调整目录权限（0777)，然后再刷新页面<br>';
    } else {
        @unlink($codeDir . '/.test.test');
    }
    if (empty($_GET['a']) || $_GET['a'] != 'create') :
?>
        <table align="center">
        <tr><th colspan="2">创建管理后台DAGGER_APP</th></tr>
        <tr>
        	<td>检测到管理后台基础代码不存在</td>
        </tr>
        <?php if (!empty($str)):?>
        <tr>
        	<td style="color:red"><?php echo $str?></td>
        </tr>
        <?php else:?>
        <tr>
        	<td>将会在/app目录下创建admin的应用（/app/admin）</td>
        </tr>
        <tr>
        	<td align="right"><a href="./index.php?a=create">创建</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <?php endif;?>
        </table>
<?php
        exit;
    endif;
    if ($_GET['a'] == 'create') {
        $rs = file_get_contents($dir . "/../../config/SysInitConfig.php");
        if (empty($rs)) {
            die("请勿更改install程序的路径！");
        }
        //写入文件
        mkdir($dir);
        file_list("./app", $dir);
        //设置config
        if (strpos($rs, "define('URL_ROOT', '/');")) {
            $rs = str_replace("define('URL_ROOT', '/');", "define('URL_ROOT', '".substr($_SERVER['SCRIPT_NAME'],0,-23)."');", $rs);
            file_put_contents($dir . "/../../config/SysInitConfig.php", $rs);
        }
    }
endif;
?>
    <table align="center">
        <tr><th>创建管理后台DAGGER_APP</th></tr>
        <tr>
        	<td><?=$GLOBALS['out'];?></td>
        </tr>
        <tr>
        	<td>检测到后台基础代码已存在<br>点击进入下一步，<a href="./step_1.php">创建模块</a></td>
        </tr>
    </table>
</div>
</body>
</html>
