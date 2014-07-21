<?php /* Smarty version Smarty-3.0.7, created on 2014-06-07 18:19:24
         compiled from "/Users/zhangwenhan/web/www/backend/missy-blue/app/admin/templates/Item.html" */ ?>
<?php /*%%SmartyHeaderCode:1706342225392e72c51bf19-94616654%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6b919a669aeab36129ce34fea5ea481d5b6937a1' => 
    array (
      0 => '/Users/zhangwenhan/web/www/backend/missy-blue/app/admin/templates/Item.html',
      1 => 1402136079,
      2 => 'file',
    ),
    '6c640511ffbf91e9057b548d5fa6992f9d903926' => 
    array (
      0 => '/Users/zhangwenhan/web/www/backend/missy-blue/app/admin/templates/layout.html',
      1 => 1400317123,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1706342225392e72c51bf19-94616654',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_createUrl')) include '/Users/zhangwenhan/web/www/backend/missy-blue/libs/view/myplugins/function.createUrl.php';
if (!is_callable('smarty_function_cycle')) include '/Users/zhangwenhan/web/www/backend/missy-blue/libs/view/plugins/function.cycle.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>后台管理系统</title>
<link rel="stylesheet" type="text/css" href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('basePath')->value, $_smarty_tpl, true);?>
/css/admin/style.css" />
<link rel="stylesheet" type="text/css" href="http://news.sina.com.cn/js/jquery_ui/css/jquery-ui-1.10.1.custom.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('basePath')->value, $_smarty_tpl, true);?>
/css/admin/batch.css">

<script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.9.1/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="http://news.sina.com.cn/js/jquery_ui/js/jquery-ui-1.10.1.custom.js"></script>
<script type="text/javascript" src="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('basePath')->value, $_smarty_tpl, true);?>
/js/jqueryPlugins.js"></script>
<script type="text/javascript" src="<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->getVariable('basePath')->value, $_smarty_tpl, true);?>
/js/admin/gsps.js"></script>

</head>

<body>
    <div class="ui-wrapper">
        <!--header begin-->
        <div class="header">
            <div class="header-logo"></div>
            <div class="header-content">
                <div class="header-userEntry">
                    <a href="#" target="_blank">切换用户</a> | 
                    <a href="#" target="_blank">设置</a> |
                    <a href="?c=admin&a=logout">注销</a>
                </div>
            </div>
        </div>
        <!--header end-->
        <!--content begin-->
        <div class="cms-content clearfix">
            <div class="contetnt-left">
            <?php $_template = new Smarty_Internal_Template("left.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
            </div>
            <div id="content" class="content-right">
            
<!-- separator start-->
<div id="separator" class="ui-separator separator-left" data-options-toggle=".contetnt-left" data-options-extend = ".content-right">
</div>
<!-- separator end-->
<!-- content start-->
<div class="cms-main">
    <!-- menubar start-->
    <div class="man-nav">
        <div class="ui-menubar clearfix">
        <ul class="ui-menu-list">
                <li class="ui-menu-item ui-menu-current opt" data-opt_type="search" data-opt_id="searchContainer" data-opt_url="/">商品
                </li>
            </ul>
            <div class="ui-menu-state">
                <a href="/">后台首页</a>/<span class="menu-state-current">商品</span>
            </div>
        </div>
    </div>
    <!--menubar end-->
    <div class="main-content">
        <div class="content-search">
            <fieldset class="ui-search">
            <form id="search_form" name="search_form" method="get">
                    <input type="hidden" name="s" value="item"/>
            <input type="hidden" name="a" value="view"/>
            <div class="ui-search-container">
                <select name="_search_field"><option value="sn">商品编号</option><option value="name">商品名称</option></select>&nbsp;&nbsp;<input hidefocus type="text" name="_search_keyword" value="" id="keyword" size="20" class="ui-input-text">&nbsp;&nbsp;<input hidefocus type="button" class="ui-input-button opt" data-opt_type="search" data-opt_id="searchContainer" data-opt_url="/" value="搜索">
            </div>
        </form>
        </fieldset>
    </div>
        <div id="searchContainer">
            <div>
                <div class="ui-toolbar">
                    <div class="toolbar-buttons">
                        <input type="submit" class="opt ui-input-button" data-opt_id="item_update_dialog" data-opt_type="add" data-opt_action="<?php echo smarty_function_createUrl(array('controller'=>'item','action'=>'create'),$_smarty_tpl);?>
" alt="添加商品" title="添加商品"value="添加商品"> 
                        <input type="button" class="opt ui-input-button" data-opt_id='sortable_tbody' data-opt_type="refresh" data-opt_url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
' value="刷新">
                    </div>
                    <div class="toolbar-pagination">
                        <table><tr><td><?php echo $_smarty_tpl->getVariable('pageStr')->value;?>
&nbsp;</td></tr></table>
                    </div>
                </div>
                <div class="ui-debug-ie6-table">
                    <table class="ui-list-table">
                        <thead>
                            <tr>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='id' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>ID</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='sn' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>商品编号</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='name' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>商品名称</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='category_id' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>栏目分类</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='size_id' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>尺寸</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='color_id' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>颜色</th>
                        <th>价格</th>
                        <th>描述</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='num' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>数量</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>更新时间</th>
                        <th>操作</th></tr>

                        </thead>
                        <tbody id="sortable_tbody" style='table-layout: fixed;'>
                            <tr></tr>
                            <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('data')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
?>
                                <tr class="<?php echo smarty_function_cycle(array('values'=>',odd'),$_smarty_tpl);?>
<?php if ($_smarty_tpl->tpl_vars['item']->value['is_del']==1){?> del<?php }?>" id="tr_<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['id'], $_smarty_tpl, true);?>
">
                        <td class="td_id"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['id'], $_smarty_tpl, true);?>
</td>
                        <td class="td_sn"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['sn'], $_smarty_tpl, true);?>
</td>
                        <td class="td_name"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['name'], $_smarty_tpl, true);?>
</td>
                        <td class="td_category_id"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['category_id'], $_smarty_tpl, true);?>
</td>
                        <td class="td_size_id"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['size_id'], $_smarty_tpl, true);?>
</td>
                        <td class="td_color_id"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['color_id'], $_smarty_tpl, true);?>
</td>
                        <td class="td_price"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['price'], $_smarty_tpl, true);?>
</td>
                        <td class="td_desc"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['desc'], $_smarty_tpl, true);?>
</td>
                        <td class="td_num"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['num'], $_smarty_tpl, true);?>
</td>
                        <td class="td_status"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['status'], $_smarty_tpl, true);?>
</td>
                        <td class="td_ctime"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['ctime'], $_smarty_tpl, true);?>
</td>
                        <td class="td_utime"><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['utime'], $_smarty_tpl, true);?>
</td>
<?php if ($_smarty_tpl->tpl_vars['item']->value['is_del']==1){?>                        <td>[<a href="javascript:void(0);" class="opt"  data-opt_id="item_update_dialog" data-opt_type="update" data-opt_url="<?php ob_start();?><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['id'], $_smarty_tpl, true);?>
<?php $_tmp1=ob_get_clean();?><?php echo smarty_function_createUrl(array('controller'=>'item','action'=>'get','params'=>array('id'=>$_tmp1)),$_smarty_tpl);?>
" data-opt_action="<?php ob_start();?><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['id'], $_smarty_tpl, true);?>
<?php $_tmp2=ob_get_clean();?><?php echo smarty_function_createUrl(array('controller'=>'item','action'=>'update','params'=>array('id'=>$_tmp2)),$_smarty_tpl);?>
" alt="商品编辑" title="商品编辑" >编辑</a>] | [<a href="javascript:void(0);" class="opt"  data-opt_id="item_logic_resume" data-opt_type="delete" data-opt_url="<?php ob_start();?><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['id'], $_smarty_tpl, true);?>
<?php $_tmp3=ob_get_clean();?><?php echo smarty_function_createUrl(array('controller'=>'item','action'=>'logic_resume','params'=>array('id'=>$_tmp3)),$_smarty_tpl);?>
" alt="商品恢复" title="商品恢复" >恢复</a>]</td><?php }else{ ?>                        <td>[<a href="javascript:void(0);" class="opt"  data-opt_id="item_update_dialog" data-opt_type="update" data-opt_url="<?php ob_start();?><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['id'], $_smarty_tpl, true);?>
<?php $_tmp4=ob_get_clean();?><?php echo smarty_function_createUrl(array('controller'=>'item','action'=>'get','params'=>array('id'=>$_tmp4)),$_smarty_tpl);?>
" data-opt_action="<?php ob_start();?><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['id'], $_smarty_tpl, true);?>
<?php $_tmp5=ob_get_clean();?><?php echo smarty_function_createUrl(array('controller'=>'item','action'=>'update','params'=>array('id'=>$_tmp5)),$_smarty_tpl);?>
" alt="商品编辑" title="商品编辑" >编辑</a>] | [<a href="javascript:void(0);" class="opt"  data-opt_id="item_logic_delete" data-opt_type="delete" data-opt_url="<?php ob_start();?><?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_smarty_tpl->tpl_vars['item']->value['id'], $_smarty_tpl, true);?>
<?php $_tmp6=ob_get_clean();?><?php echo smarty_function_createUrl(array('controller'=>'item','action'=>'logic_delete','params'=>array('id'=>$_tmp6)),$_smarty_tpl);?>
" alt="商品删除" title="商品删除" >删除</a>]</td><?php }?></tr>

                            <?php }} ?> 
                        </tbody>
                        <thead>
                            <tr>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='id' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>ID</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='sn' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>商品编号</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='name' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>商品名称</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='category_id' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>栏目分类</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='size_id' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>尺寸</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='color_id' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>颜色</th>
                        <th>价格</th>
                        <th>描述</th>
                       <th class='opt sortable' type='button' data-opt_type='order' data-opt_name='num' data-opt_id='sortable_tbody' data-url='http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
'><div class='sortoptions'></div>数量</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>更新时间</th>
                        <th>操作</th></tr>

                        </thead>
                    </table>
                </div>
                <div class="ui-toolbar">
                    <div class="toolbar-pagination">
                        <table><tr><td><?php echo $_smarty_tpl->getVariable('pageStr')->value;?>
&nbsp;</td></tr></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--form start-->
<div id="item_update_dialog" style="display:none;" title="商品编辑">
<form class="ui-form" action="" method="post" enctype="multipart/form-data"><table align="center" width="100%"><tbody>
<tr class="field-group"><td class="field-header">商品编号：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="sn" value="" /></td></tr>
<tr class="field-group"><td class="field-header">商品名称：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="name" value="" /></td></tr>
<tr class="field-group"><td class="field-header">栏目分类：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="category_id" value="" /></td></tr>
<tr class="field-group"><td class="field-header">尺寸：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="size_id" value="" /></td></tr>
<tr class="field-group"><td class="field-header">颜色：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="color_id" value="" /></td></tr>
<tr class="field-group"><td class="field-header">价格：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="price" value="" /></td></tr>
<tr class="field-group"><td class="field-header">描述：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="desc" value="" /></td></tr>
<tr class="field-group"><td class="field-header">数量：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="num" value="" /></td></tr>
<tr class="field-group"><td class="field-header">状态：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="status" value="" /></td></tr>
<tr class="field-group"><td class="field-header">上传图片：</td><td class="field-content"><input type="file" class="ui-text-dialog" name="image[]" value="" /></td><td class="field-header">位置：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="position[]" value="" /></td> <td class="field-header">尺寸：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="size[]" value="" /></td></tr>
<tr id="aha" class="field-group"><td class="field-header"><button id="add_img" type="button">Click Me!</button> </td></tr>
<tr class="field-group" style="border-bottom:0px;padding-bottom:0px;"><td class="field-header"></td><td class="field-content" style="text-align: center; display: block;"><input type="submit" class="ui-button-submit" id="submit" value="提交"><td></tr></tbody></table></form>
</div>
<!--form end-->

<script type="text/javascript">
$('#add_img').click(function(){
        $('#aha').before('<tr class="field-group"><td class="field-header">上传图片：</td><td class="field-content"><input type="file" class="ui-text-dialog" name="image[]" value="" /></td><td class="field-header">位置：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="position[]" value="" /></td> <td class="field-header">尺寸：</td><td class="field-content"><input type="text" class="ui-text-dialog" name="size[]" value="" /></td></tr>');
});
    function beginOpt(obj) {
    switch (obj.data("opt_id")) {
        case "item_delete":
            if(!confirm("确定要删除么？")) {
                return false;
        }
        break;
    }
    return true;
    }

    function endOpt(obj, data) {
    switch (obj.data("opt_id")) {
            //更新
        case "item_update_dialog":
                if (data.status.code == "0") {
                var result = data.data;
            for (var i in result) {
                obj.parent("td").parent("tr").find(".td_" + i).html(result[i]);
            }
        }
        break;
        //删除
        case "item_delete":
                if (data.status.code == "0") {
                obj.parent("td").parent("tr").addClass("del");
                    //reloadList('http://<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['HTTP_HOST'], $_smarty_tpl, true);?>
<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', $_SERVER['REQUEST_URI'], $_smarty_tpl, true);?>
');
        }
        break;
        //逻辑删除
        case "item_logic_delete":
                if (data.status.code == "0") {
                    obj.data("opt_id", obj.data("opt_id").replace("logic_delete", "logic_resume"));
                    obj.data("opt_url", obj.data("opt_url").replace("logic_delete", "logic_resume"));
                    obj.attr("alt", obj.attr("alt").replace("删除", "恢复"));
                    obj.attr("title", obj.attr("title").replace("删除", "恢复"));
            obj.parent("td").parent("tr").addClass("del");
            obj.html("恢复");
        }
        break;
        //逻辑恢复
        case "item_logic_resume":
            if (data.status.code == '0') {
                obj.data("opt_id", obj.data("opt_id").replace("logic_resume", "logic_delete"));
            obj.data("opt_url", obj.data("opt_url").replace("logic_resume", "logic_delete"));
            obj.attr("alt", obj.attr("alt").replace("恢复", "删除"));
            obj.attr("title", obj.attr('title').replace("恢复", "删除"));
            obj.parent("td").parent("tr").removeClass("del");
            obj.html("删除");
        }
        break;
        }
    return true;
    }

    function endGetData(obj, data) {
        switch (obj.data('opt_id')) {
            case '***':
                //do samething;
                break;
        }
        return true;
    }
</script>

            </div>
        </div>
        <!--content end-->
        <!-- footer -->
        <div class="footer">
            <p>Copyright &copy; 1996-<?php echo Smarty_Internal_Filter_Handler::runFilter('variable', date('Y'), $_smarty_tpl, true);?>
 SINA Corporation, All Rights Reserved</p>
            <p></p>
            <p>新浪公司 <a href="http://www.sina.com.cn/intro/copyright.shtml">版权所有</a></p>
        </div>
        <!-- end footer -->
    </div>
    <script type="text/javascript" src="/js/admin/common.js"></script>
</body>
</html>
