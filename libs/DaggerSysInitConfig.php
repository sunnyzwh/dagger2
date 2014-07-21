<?php
define('DAGGER_VERSION', '2.0');

//*******************************************************应用名称********************************************************
defined('DAGGER_APP_EXAMPLE')       || define('DAGGER_APP_EXAMPLE',         'example');
defined('DAGGER_APP_SITE')       || define('DAGGER_APP_SITE',         'site');
defined('DAGGER_APP_ADMIN')         || define('DAGGER_APP_ADMIN',           'admin');


//*******************************************************运行平台********************************************************
if (isset($_SERVER['HTTP_APPNAME'])) {
    defined('DAGGER_PLATFORM')      || define('DAGGER_PLATFORM',            'sae');//可指定为sae，其他情况为dpool。
} else {
    defined('DAGGER_PLATFORM')      || define('DAGGER_PLATFORM',            'dpool');
}

//*******************************************************目录设置********************************************************
//框架基础路径设置
defined('DAGGER_PATH_APP')          || define('DAGGER_PATH_APP',            DAGGER_PATH_ROOT . 'app/');//应用所在目录
defined('DAGGER_PATH_CONFIG')       || define('DAGGER_PATH_CONFIG',         DAGGER_PATH_ROOT . 'config/');//config目录
defined('DAGGER_PATH_MODEL')        || define('DAGGER_PATH_MODEL',          DAGGER_PATH_ROOT . 'model/');//model目录
defined('DAGGER_PATH_LIBS')         || define('DAGGER_PATH_LIBS',           DAGGER_PATH_ROOT . 'libs/');//框架库目录
defined('DAGGER_PATH_LIBS_MODEL')   || define('DAGGER_PATH_LIBS_MODEL',     DAGGER_PATH_LIBS . 'model/');//框架model基类目录
defined('DAGGER_PATH_LIBS_VIEW')    || define('DAGGER_PATH_LIBS_VIEW',      DAGGER_PATH_LIBS . 'view/');//框架view基类目录
defined('DAGGER_PATH_LIBS_CTL')     || define('DAGGER_PATH_LIBS_CTL',       DAGGER_PATH_LIBS . 'controller/');//框架controller基类目录
defined('DAGGER_PATH_LIBS_PLT')     || define('DAGGER_PATH_LIBS_PLT',       DAGGER_PATH_LIBS . 'pagelet/');//框架pegelet基类目录
defined('DAGGER_PATH_MYPLUGINS')    || define('DAGGER_PATH_MYPLUGINS',      DAGGER_PATH_LIBS_VIEW . 'myplugins');//smarty扩展插件目录

//数据目录
if (DAGGER_PLATFORM == 'sae') {
    defined('DAGGER_PATH_DATA')     || define('DAGGER_PATH_DATA',           'saestor://data/');
    defined('DAGGER_PATH_CACHE')    || define('DAGGER_PATH_CACHE',          'saemc://cache/');
    //defined('DAGGER_PATH_APPLOG') || define('DAGGER_PATH_APPLOG',         'SaeStorage://log/');//SAE不提供追加写入，日志在程序中使用的sae_debug()
} else {
    defined('DAGGER_PATH_DATA')     || define('DAGGER_PATH_DATA',           DAGGER_PATH_ROOT . 'data/dagger/');//数据目录,格式：/data1/apache/data
    defined('DAGGER_PATH_CACHE')    || define('DAGGER_PATH_CACHE',          DAGGER_PATH_ROOT . 'cache/dagger/');//缓存目录,格式：/data1/apache/cache
    defined('DAGGER_PATH_APPLOG')   || define('DAGGER_PATH_APPLOG',         DAGGER_PATH_ROOT . 'applogs/dagger/');//缓存目录,格式：/data1/apache/applogs
}

//*******************************************************模板引擎******************************************************
defined('DAGGER_TEMPLATE_ENGINE')   || define('DAGGER_TEMPLATE_ENGINE',     'smarty');//默认不使用模版引擎,使用smarty时值为：smarty

//*******************************************************数据库*******************************************************
defined('DAGGER_DB_DEFAULT')        || define('DAGGER_DB_DEFAULT',          'default');//默认数据库

//*******************************************************调试DEBUG********************************************************
defined('DAGGER_XHPROF')            || define('DAGGER_XHPROF',              0);//xhprof开启状态
defined('DAGGER_XHPROF_PR')         || define('DAGGER_XHPROF_PR',           1000); // xhprof 调用概率
defined('DAGGER_ENV')               || define('DAGGER_ENV',                 'dev');//dev:开发模式|test:测试模式|product:线上模
defined('DAGGER_DEBUG')             || define('DAGGER_DEBUG',               1);//DEBUG模//式
defined('DAGGER_ONLINE_DEBUG')      || define('DAGGER_ONLINE_DEBUG',        0);//DEBUG模//式
defined('DAGGER_DEBUG_ARG_NAME')    || define('DAGGER_DEBUG_ARG_NAME',      'debug');//debug过滤get参数名
defined('DAGGER_TIMEOUT')           || define('DAGGER_TIMEOUT',             5);// 默认脚本运行超时报警时间（秒）
defined('DAGGER_DBCONNECT_TIMEOUT') || define('DAGGER_DBCONNECT_TIMEOUT',   1);// 默认数据库连接时间过长报警时间（秒）
defined('DAGGER_MCCONNECT_TIMEOUT') || define('DAGGER_MCCONNECT_TIMEOUT',   0.5);// 默认MC连接时间过长报警时间（秒）

//******************************************************MC设置**************************************************************
defined('DAGGER_MC_DEFAULT')        || define('DAGGER_MC_DEFAULT',          'default');//默认使用的MC组
defined('DAGGER_MC_KEY_PREFIX')     || define('DAGGER_MC_KEY_PREFIX',       'dagger_');//项目MC key前缀，避免多个项目公用MC时key冲突

//******************************************************URL路由开关**********************************************************
defined('DAGGER_ROUTER')            || define('DAGGER_ROUTER',              1); //1为打开，设置路由打开前请确认rewrite规则：RewriteEngine On RewriteCond %{REQUEST_URI} !^/(css(.*)|js(.*)|image(.*)|(.*).php)$ RewriteRule (.*) /index.php/$1 [L]已添加，具体配置请到RouterConfig进行设置

//******************************************************控制器\方法参数名\请求串*********************************************
//修改参数名需对应修改apache的ReWrite规则。
defined('DAGGER_APP')               || define('DAGGER_APP','p');
defined('DAGGER_CONTROLLER')        || define('DAGGER_CONTROLLER','c');
defined('DAGGER_ACTION')            || define('DAGGER_ACTION','a');
defined('DAGGER_APP_PREFIX')        || define('DAGGER_APP_PREFIX', 'app_');

//******************************************************开始运行时间*********************************************************
defined('DAGGER_STARTTIME')         || define('DAGGER_STARTTIME',           microtime(true));//程序开始运行时间
