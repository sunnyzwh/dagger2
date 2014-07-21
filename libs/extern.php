<?php
/**
 * 老项目使用libs库需要include该文件 
 */

define('EXTERN', 1);
define('DAGGER_PATH_ROOT', rtrim(dirname(__FILE__), "/") . "/../");
ob_start();
//系统初始化定义
require(DAGGER_PATH_ROOT . 'config/SysInitConfig.php');
//系统配置
require(DAGGER_PATH_ROOT . 'libs/DaggerSysInitConfig.php');
//__autoload函数
require(DAGGER_PATH_LIBS . 'basics.php');
//载入数据存储配置
require(DAGGER_PATH_CONFIG . 'DBConfig.php');
