<?php
/**
 * dagger项目初始化
 */

session_start();
ob_start();
//用户配置
require(DAGGER_PATH_ROOT . 'config/SysInitConfig.php');
//系统配置
require(DAGGER_PATH_ROOT . 'libs/DaggerSysInitConfig.php');
//__autoload函数
require(DAGGER_PATH_LIBS . 'basics.php');
//载入数据存储配置
require(DAGGER_PATH_CONFIG . 'DBConfig.php');

if(1 === DAGGER_XHPROF && defined('DAGGER_ALARM_XHPROF_API')) {
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    define('DAGGER_XHPROF_ID', uniqid());
}

//静态URL解析规则
BaseModelRouter::route();

$class = BaseModelCommon::getFormatName($_GET[DAGGER_CONTROLLER], 'class');
$class .= 'Controller';
$controller = new $class($_GET[DAGGER_CONTROLLER], $_GET[DAGGER_ACTION]);
$controller->runCommand();

if(defined('DAGGER_XHPROF') && 1 === DAGGER_XHPROF) {
    echo '<br /><a href="' . BaseModelDebug::getXhprofUrl() .'" target="_blank" >xhprof</a>';
}
