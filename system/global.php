<?php
/**
 * All rights reserved.
 * 后台运行主程序
 * @author          wangxin <wangxin3@staff.sina.com.cn>
 * @time            2011/3/2 15:03
 * @version         Id: 0.9
*/
set_time_limit(0);
ini_set('display_error', 1);
declare(ticks = 1);

if (isset($_SERVER['HTTP_HOST'])) {
    //防止被http调用
    exit();
}
define('QUEUE', 1);
define('DAGGER_DEBUG_ARG_NAME', 'debug');
// 命令行输出数组表格截字符长度，中文字符请*2处理。例如2个中文，则是4个字符长度
define('QUEUE_DEBUG_LEN', 10);

//@include 'QueueTaskCtl.php';//队列任务,在配置完成监控大厅，并且设置完成DAGGER_ALARM_URL后再开启
class_exists('QueueTaskCtl', false) && define('QUEUE_CTL', 1);

if (!empty($_SERVER['argv'])) {
    foreach ($_SERVER['argv'] as $k => $arg) {
        if(preg_match("/^--([^=]+)=?(.*?)$/s", $arg, $match)) {
            $_GET[$match[1]] = $match[2];
            $match[1] === DAGGER_DEBUG_ARG_NAME && $pos = $k;
        }
    }
}

define('DAGGER_PATH_ROOT', rtrim(dirname(__FILE__), '/') . '/../');
require DAGGER_PATH_ROOT . 'config/SysInitConfig.php';//用户配置
require DAGGER_PATH_ROOT . 'libs/DaggerSysInitConfig.php';//系统配置
require DAGGER_PATH_LIBS . 'basics.php';//__autoload函数
require DAGGER_PATH_ROOT . 'config/DBConfig.php';//载入数据库配置

if(1 === DAGGER_XHPROF) {
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    define('DAGGER_XHPROF_ID', uniqid());
}

function daggerSignalHandler($signal){
    defined('QUEUE_CTL') && QueueTaskCtl::end(1, 'signal catched');
    switch($signal) {
        case SIGTERM:
            Message::showError('Caught SIGTERM');
            exit;
        case SIGINT:
            Message::showError('Caught SIGINT');
            exit;
    }
}

if(function_exists('pcntl_signal')){
    pcntl_signal(SIGTERM, 'daggerSignalHandler');
    pcntl_signal(SIGINT, 'daggerSignalHandler');
}

defined('QUEUE_CTL') && QueueTaskCtl::begin();
