<?php
error_reporting(E_ALL ^E_NOTICE);
ob_start();
session_start();
if (!in_array($_SERVER['SERVER_ADDR'], array('127.0.0.1'))) {
    die('server IP is not allow, please edit in ./install/admin/common.php');
}
define('DAGGER_PATH_ROOT', rtrim(dirname(__FILE__), "/") . "/../../");
/**
* Initial System Configure
*/
require DAGGER_PATH_ROOT . 'config/SysInitConfig.php';//ϵͳdefine
require DAGGER_PATH_ROOT . 'libs/DaggerSysInitConfig.php';//ϵͳdefine
require DAGGER_PATH_LIBS . 'basics.php';//__autoload����
require DAGGER_PATH_ROOT . "config/DBConfig.php";//�������ݴ洢����
$_SESSION['dir'] = DAGGER_PATH_ROOT;

