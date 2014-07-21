<?php
/*
 * by wangxin3
 * 获取当前app的baseUrl
 */
function smarty_function_baseUrl($param, &$smarty)
{
    return RouterConfig::$baseUrl[Configure::$app]; 
}
