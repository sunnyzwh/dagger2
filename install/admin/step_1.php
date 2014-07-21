<?php
require("./common.php");
?>
<html>
<head>
<title>模板构架</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://www.sinaimg.cn/jslib/js/jquery.1.8.2.js"></script>
<script>
$(document).ready(function(){
    $.getScript("./setting/step_1.js");
});
</script>
<style>
body {text-align:center;padding-top:50px;background-color:#F7E8AC;}
table {font-size:12px; line-height:20px;border-color:#FF9900; border-style:groove}
th {background-color:#FF9900; font-size:14px; font-weight:bold;}
td {padding-left:5px; border-style:inherit; border-right:1px dashed #FF9900;}
</style>
</head>
<body>
<div style="margin:0 auto;width:550px;">
<form method="post" action="step_2.php">
		<table align="center">
        	<tr><th colspan="2">项目表结构获取(填写开发数据库配置)</th></tr>
			<tr>
				<td>数据库主机名称</td>
				<td><input type="text" name="host" size="15" value="127.0.0.1" />
					指定有项目表结构的库
				</td>
			</tr>
			<tr>
				<td>数据库名称</td>
				<td><input type="text" name="dbname" size="15" value="cinderella" /> &nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td>数据库用户名</td>
				<td><input type="text" name="user" size="15" value="root" />			
				</td>
			</tr>
			<tr>
				<td style="border-bottom:1px dashed #FF9900;">数据库密码</td>
				<td style="border-bottom:1px dashed #FF9900;"><input type="password" name="pwd" size="15" value="" />			
				</td>
			</tr>
			<tr>
				<td style="border-bottom:1px dashed #FF9900;">DAGGER_APP名称</td>
                <td style="border-bottom:1px dashed #FF9900;">
    				<select name="app"><option>admin</option></select>
                    生成的后台app名称就为admin，不用选择
				</td>
			</tr>
        	<tr><th colspan="2">该模块正式环境使用数据库配置</th></tr>
			<tr>
				<td style="border-bottom:1px dashed #FF9900;">使用数据库</td>
				<td style="border-bottom:1px dashed #FF9900;">
                    <select name="db_mark">
                    <?php foreach(DBConfig::$config['mysql'] as $k=>$v):?>
                    <option value="<?php echo $k;?>"><?php echo $k;?>库</option>
                    <?php endforeach;?>
                    </select>
                    <br>
                    可在/config/DBConfig.php里的配置多库信息
                    
                    <!--<select name="db_mark"><option>bbs</option><option>clubsession</option><option>bbsuser</option></select>-->		
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="hidden" name="step" value="0"/>
					<input type="submit" class="button" value="下一步" />&nbsp;&nbsp;
					<input type="reset" class="button" value="重 设" />
				</td>
			</tr>
		</table>
	</form>
</div>
</body>
</html>
