<?php /* Smarty version Smarty-3.0.7, created on 2014-06-07 15:46:15
         compiled from "/Users/zhangwenhan/web/www/backend/missy-blue/app/admin/templates/page/style_0.html" */ ?>
<?php /*%%SmartyHeaderCode:8496924575392c3471bd727-03295383%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'efab02af214bf02f7675a05c798610d09305ea9f' => 
    array (
      0 => '/Users/zhangwenhan/web/www/backend/missy-blue/app/admin/templates/page/style_0.html',
      1 => 1400317123,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8496924575392c3471bd727-03295383',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_math')) include '/Users/zhangwenhan/web/www/backend/missy-blue/libs/view/plugins/function.math.php';
?>         
<style>
/* 分页 */
.pagebox{overflow:hidden; zoom:1; font-size:12px; font-size:12px; font-family:Arial;}
.pagebox span{float:left; margin-right:4px; overflow:hidden; text-align:center; background:#fff;}
.pagebox span a{display:block; overflow:hidden; zoom:1; _float:left;}
.pagebox span.pagebox_all_num{padding:0 5px; height:21px; line-height:21px; color:#8e9197; cursor:default; background:none;}
.pagebox span.pagebox_pre_nolink{border:1px #e8e8ec solid; height:21px; line-height:21px; padding:0 9px 0 20px; color:#dcdcdc; background:url(http://www.sinaimg.cn/dy/deco/2008/0430/content_page_box_bg_1.gif) no-repeat 0 0 #fff; cursor:default;}
.pagebox span.pagebox_pre{color:#333; height:23px;}
.pagebox span.pagebox_pre a,.pagebox span.pagebox_pre a:visited{border:1px #dcdcdc solid; color:#333; text-decoration:none; padding:0 9px 0 20px; background:url(http://www.sinaimg.cn/dy/deco/2008/0430/content_page_box_bg_1.gif) no-repeat 0 -50px; cursor:pointer; height:21px; line-height:21px;}
.pagebox span.pagebox_pre a:hover,.pagebox span.pagebox_pre a:active{color:#fff; background:url(http://www.sinaimg.cn/dy/deco/2008/0430/content_page_box_bg_1.gif) no-repeat 0 -100px #8e9197; border:1px #8e9197 solid;}
.pagebox span.pagebox_num_nonce{padding:0 5px; height:21px; line-height:21px; color:#8e9197; cursor:default; background:none;}
.pagebox span.pagebox_num{color:#333; height:23px;}
.pagebox span.pagebox_num a,.pagebox span.pagebox_num a:visited{border:1px #dcdcdc solid; color:#333; text-decoration:none; padding:0 6px; cursor:pointer; height:21px; line-height:21px;}
.pagebox span.pagebox_num a:hover,.pagebox span.pagebox_num a:active{color:#fff; border:1px #8e9197 solid; background:#8e9197;}
.pagebox span.pagebox_num_ellipsis{color:#8e9197; width:26px; background:none;}
.pagebox span.pagebox_next_nolink{border:1px #e8e8ec solid; height:21px; line-height:21px; padding:0 20px 0 9px; color:#dcdcdc; background:url(http://www.sinaimg.cn/dy/deco/2008/0430/content_page_box_bg_1.gif) no-repeat 100% -25px #fff; cursor:default;}
.pagebox span.pagebox_next a,.pagebox span.pagebox_next a:visited{border:1px #dcdcdc solid; color:#333; text-decoration:none; padding:0 20px 0 9px; background:url(http://www.sinaimg.cn/dy/deco/2008/0430/content_page_box_bg_1.gif) no-repeat 100% -75px; cursor:pointer; height:21px; line-height:21px;}
.pagebox span.pagebox_next a:hover,.pagebox span.pagebox_next a:active{color:#fff; background:url(http://www.sinaimg.cn/dy/deco/2008/0430/content_page_box_bg_1.gif) no-repeat 100% -125px #8e9197; border:1px #8e9197 solid;}
</style>
<span class="pagebox">
	<span class="pagebox_all_num">共<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('totalPage')->value, $_smarty_tpl, true);?>
页</span>
	<?php if ($_smarty_tpl->getVariable('page')->value!=1){?><span class="pagebox_pre"><a href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('paramStr')->value, $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('pName')->value, $_smarty_tpl, true);?>
=<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('prePage')->value, $_smarty_tpl, true);?>
">上一页</a></span><?php }else{ ?><span class="pagebox_pre_nolink">上一页</span><?php }?>
	<?php if ($_smarty_tpl->getVariable('page')->value>3){?><span class="pagebox_num"><a href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('paramStr')->value, $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('pName')->value, $_smarty_tpl, true);?>
=<?php echo smarty_function_math(array('equation'=>"x - y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>3),$_smarty_tpl);?>
"><?php echo smarty_function_math(array('equation'=>"x - y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>3),$_smarty_tpl);?>
</a></span><?php }?>
	<?php if ($_smarty_tpl->getVariable('page')->value>2){?><span class="pagebox_num"><a href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('paramStr')->value, $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('pName')->value, $_smarty_tpl, true);?>
=<?php echo smarty_function_math(array('equation'=>"x - y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>2),$_smarty_tpl);?>
"><?php echo smarty_function_math(array('equation'=>"x - y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>2),$_smarty_tpl);?>
</a></span><?php }?>
	<?php if ($_smarty_tpl->getVariable('page')->value>1){?><span class="pagebox_num"><a href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('paramStr')->value, $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('pName')->value, $_smarty_tpl, true);?>
=<?php echo smarty_function_math(array('equation'=>"x - y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>1),$_smarty_tpl);?>
"><?php echo smarty_function_math(array('equation'=>"x - y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>1),$_smarty_tpl);?>
</a></span><?php }?>
	<span class="pagebox_num_nonce"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('page')->value, $_smarty_tpl, true);?>
</span>
	<?php if ($_smarty_tpl->getVariable('lastPage')->value-$_smarty_tpl->getVariable('page')->value>=1){?><span class="pagebox_num"><a href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('paramStr')->value, $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('pName')->value, $_smarty_tpl, true);?>
=<?php echo smarty_function_math(array('equation'=>"x + y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>1),$_smarty_tpl);?>
"><?php echo smarty_function_math(array('equation'=>"x + y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>1),$_smarty_tpl);?>
</a></span><?php }?>
	<?php if ($_smarty_tpl->getVariable('lastPage')->value-$_smarty_tpl->getVariable('page')->value>=2){?><span class="pagebox_num"><a href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('paramStr')->value, $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('pName')->value, $_smarty_tpl, true);?>
=<?php echo smarty_function_math(array('equation'=>"x + y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>2),$_smarty_tpl);?>
"><?php echo smarty_function_math(array('equation'=>"x + y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>2),$_smarty_tpl);?>
</a></span><?php }?>
	<?php if ($_smarty_tpl->getVariable('lastPage')->value-$_smarty_tpl->getVariable('page')->value>=3){?><span class="pagebox_num"><a href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('paramStr')->value, $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('pName')->value, $_smarty_tpl, true);?>
=<?php echo smarty_function_math(array('equation'=>"x + y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>3),$_smarty_tpl);?>
"><?php echo smarty_function_math(array('equation'=>"x + y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>3),$_smarty_tpl);?>
</a></span><?php }?>
	<?php if ($_smarty_tpl->getVariable('lastPage')->value-$_smarty_tpl->getVariable('page')->value>=4){?><span class="pagebox_num"><a href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('paramStr')->value, $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('pName')->value, $_smarty_tpl, true);?>
=<?php echo smarty_function_math(array('equation'=>"x + y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>4),$_smarty_tpl);?>
"><?php echo smarty_function_math(array('equation'=>"x + y",'x'=>$_smarty_tpl->getVariable('page')->value,'y'=>4),$_smarty_tpl);?>
</a></span><?php }?>
	<?php if ($_smarty_tpl->getVariable('lastPage')->value-$_smarty_tpl->getVariable('page')->value>=1){?><span class="pagebox_next"><a href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('paramStr')->value, $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('pName')->value, $_smarty_tpl, true);?>
=<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('nextPage')->value, $_smarty_tpl, true);?>
">下一页</a></span><?php }else{ ?><span class="pagebox_next_nolink">下一页</span><?php }?>
</span>
