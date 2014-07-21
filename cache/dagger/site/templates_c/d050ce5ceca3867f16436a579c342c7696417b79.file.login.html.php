<?php /* Smarty version Smarty-3.0.7, created on 2014-06-07 17:59:37
         compiled from "/Users/zhangwenhan/web/www/backend/missy-blue/app/site/templates/login.html" */ ?>
<?php /*%%SmartyHeaderCode:16671173215392e28938b8f3-84339494%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd050ce5ceca3867f16436a579c342c7696417b79' => 
    array (
      0 => '/Users/zhangwenhan/web/www/backend/missy-blue/app/site/templates/login.html',
      1 => 1401599801,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16671173215392e28938b8f3-84339494',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_createUrl')) include '/Users/zhangwenhan/web/www/backend/missy-blue/libs/view/myplugins/function.createUrl.php';
?><!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>login</title>
</head>
<body>
    <form action="<?php echo smarty_function_createUrl(array('controller'=>'user','action'=>'login'),$_smarty_tpl);?>
" method="post">
        email:<input type="text"  name="email"  /> <br/>
        password:<input type="text"  name="password"  /> <br/>
        <input type="submit"  value="登陆" />
    </form>
    <a href="<?php echo smarty_function_createUrl(array('controller'=>'user','action'=>'find_password'),$_smarty_tpl);?>
"> 找回密码 </a>
	
</body>
</html>
