<?php
function smarty_function_createUrl($param, &$smarty)
{
    $project = isset($param['project']) ? $param['project'] : '';
    $controller = isset($param['controller']) ? $param['controller'] : '';
    $action = isset($param['action']) ? $param['action'] : '';
    $baseUrl = isset($param['baseUrl']) ? $param['baseUrl'] : '';
    $params = isset($param['params']) ? $param['params'] : '';
    $delParams = isset($param['delParams']) ? $param['delParams'] : '';
    $addParams = isset($param['addParams']) ? $param['addParams'] : '';
    $baseUrl = isset($param['baseUrl']) ? $param['baseUrl'] : '';
    if(!empty($delParams)){
        return Router::delUrlParams($controller, $action, $delParams, $project, $baseUrl);
    }
    if(!empty($addParams)){
        return Router::addUrlParams($controller, $action, $addParams, $project, $baseUrl);
    }
    return Router::createUrl($controller, $action, $params, $project, $baseUrl);
}
