<?php
function smarty_function_array($param, &$smarty)
{
    $arr = $param['arr'];
    $key = $param['key'];
	return $arr[$key];
}
?>