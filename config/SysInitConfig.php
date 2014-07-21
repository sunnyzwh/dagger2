<?php

//*******************************************************系统配置********************************************************
define('DAGGER_ALARM_LOG_API',          'http://alarm.dagger.com/log.php'); //监控大厅域名，监控大厅下载地址：
define('DAGGER_ALARM_TMPLOG_API',       'http://alarm.dagger.com/tmplog.php'); //监控大厅域名，监控大厅下载地址：
define('DAGGER_ALARM_DEBUG_API',        'http://alarm.dagger.com/debug.php');
define('DAGGER_ALARM_XHPROF_API',       'http://alarm.dagger.com/xhprof.php');
define('DAGGER_ALARM_XHPROF_SHOW_URL',  'http://alarm.dagger.com/tools/xhprof_html/index.php');

//define('DAGGER_TEMPLATE_ENGINE', 'smarty');

//******************************************************允许的POST REFERER***************************************************
$_SERVER['SERVER_ACCEPT_REFERER'] = array('missy-blue.com');
