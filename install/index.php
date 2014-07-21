<?php
/**
 * All rights reserved.
 * 环境检测
 * @author          wangxin <wx012@126.com>
 * @time            2011/3/9 17:24
 * @version         Id: 0.9
*/
$str = '';
?>
<html>
<head>
<title>控制代码</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<style>
body {text-align:center;padding-top:50px;background-color:#F7E8AC;}
table {font-size:12px; line-height:20px;border-color:#FF9900; border-style:groove;width:800px;}
th {background-color:#FF9900; font-size:14px; font-weight:bold;}
td {padding-left:5px; border-style:inherit}
.text_red {color:#FF0000;padding-left:10px;font-size:14px;}
.text_red_1 {color:#999;padding-left:10px;}
.text_green {color:#41B200;padding-left:10px;}
.text_a {color:#999;padding-left:30px;}
</style>
</head>
<body>
<div style="margin:0 auto;width:1000px;text-align:center;">
    <table align="center">
        <tr><th>代码生成器</th></tr>
        <tr>
        	<td>
                <a href="./check.php">环境检测</a>
            </td>
        </tr>
        <tr>
        	<td>
                <a href="./admin/index.php">后台模块生成</a>
            </td>
        </tr>
        <tr>
        	<td>
                <a href="./db/index.php">DB类生成</a>
            </td>
        </tr>
        <tr>
        	<td><?=$str?></td>
        </tr>
    </table>
</div>
</body>
</html>
