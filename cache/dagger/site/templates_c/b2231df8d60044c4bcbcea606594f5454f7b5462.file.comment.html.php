<?php /* Smarty version Smarty-3.0.7, created on 2014-06-14 16:12:49
         compiled from "/Users/zhangwenhan/web/www/backend/missy-blue/app/site/templates/comment.html" */ ?>
<?php /*%%SmartyHeaderCode:484032143539c0401bf6e18-59985151%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b2231df8d60044c4bcbcea606594f5454f7b5462' => 
    array (
      0 => '/Users/zhangwenhan/web/www/backend/missy-blue/app/site/templates/comment.html',
      1 => 1402733566,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '484032143539c0401bf6e18-59985151',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_createUrl')) include '/Users/zhangwenhan/web/www/backend/missy-blue/libs/view/myplugins/function.createUrl.php';
?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Comment</title>
</head>
<body>
<form action="<?php echo smarty_function_createUrl(array('controller'=>'comment','action'=>'add'),$_smarty_tpl);?>
" method="post">

    star:<input type="text"  name="star"  /> <br/><br/>
    content:<textarea name="content" cols="50" rows="10"> </textarea> <br/><br/>
    order_id:<input type="text"  name="order_id"  /> <br/><br/>
    item_id:<input type="text"  name="item_id"  /> <br/><br/>
    parent_id:<input type="text"  name="parent_id"  /> <br/><br/>

    <input type="submit"  value="添加评论" />
</form>
<br/>
<span>##########################################################################</span>
<br/>
<form action="<?php echo smarty_function_createUrl(array('controller'=>'comment','action'=>'modify'),$_smarty_tpl);?>
" method="post">

    comment id:<input type="text"  name="id"  /> <br/><br/>
    star:<input type="text"  name="star"  /> <br/><br/>
    content:<textarea name="content" cols="50" rows="10"> </textarea> <br/><br/>

    <input type="submit"  value="修改评论" />
</form>
</body>
</html>