<?php /* Smarty version Smarty-3.0.7, created on 2014-06-14 14:30:30
         compiled from "/Users/zhangwenhan/web/www/backend/missy-blue/app/admin/templates/left.html" */ ?>
<?php /*%%SmartyHeaderCode:611926147539bec0604c207-13318892%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2500a64b55407e6ad4a402f576366b3f3c5c80d2' => 
    array (
      0 => '/Users/zhangwenhan/web/www/backend/missy-blue/app/admin/templates/left.html',
      1 => 1402727411,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '611926147539bec0604c207-13318892',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_createUrl')) include '/Users/zhangwenhan/web/www/backend/missy-blue/libs/view/myplugins/function.createUrl.php';
?><!-- 树状菜单 -->
<!--
<h1 class="item1">XX管理</h1>
<div class="block">
    <ul>
        <li><a href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', @URL_ROOT, $_smarty_tpl, true);?>
index.php?c=xx&a=xx">xx管理</a></li>
    </ul>
</div>
-->
<div id="menutree" class="ui-menutree">
<!--
    <h1 class="menu-itemHeader menu-extension">测试管理</h1>
    <div class="menu-itemCont">
        <ul>
            <li><a href="<?php echo smarty_function_createUrl(array('controller'=>'test','action'=>'view'),$_smarty_tpl);?>
">测试列表</a></li>
        </ul>
    </div>
-->
    <h1 class="menu-itemHeader menu-extension">管理用户管理</h1>
<div class="menu-itemCont">
    <ul>
        <li><a href="<?php echo smarty_function_createUrl(array('controller'=>'manage_user','action'=>'view'),$_smarty_tpl);?>
">管理用户列表</a></li>
    </ul>
</div>
<h1 class="menu-itemHeader menu-extension">商品管理</h1>
<div class="menu-itemCont">
    <ul>
        <li><a href="<?php echo smarty_function_createUrl(array('controller'=>'item','action'=>'view'),$_smarty_tpl);?>
">商品列表</a></li>
    </ul>
</div>
<h1 class="menu-itemHeader menu-extension">评论管理</h1>
<div class="menu-itemCont">
    <ul>
        <li><a href="<?php echo smarty_function_createUrl(array('controller'=>'comment','action'=>'view'),$_smarty_tpl);?>
">评论列表</a></li>
    </ul>
</div>
</div>
