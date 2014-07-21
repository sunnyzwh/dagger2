<html>
<head>
<title>DB类创建</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
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
        	<tr><th colspan="2">项目表结构获取</th></tr>
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
