<?php
/*
 * by wangxin3
 * ��ȡ��ǰapp��baseUrl
 */
function smarty_function_baseUrl($param, &$smarty)
{
    return RouterConfig::$baseUrl[Configure::$app]; 
}
